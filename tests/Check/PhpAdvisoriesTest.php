<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Check;

use Neo\WebsitecheckBundle\Check\PhpAdvisories;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** PHP versions against the advisory of 21 Oct 2026 and the supported branches, on a fixed day. */
#[CoversClass(PhpAdvisories::class)]
final class PhpAdvisoriesTest extends TestCase
{
    /** @return iterable<string, array{string, string, string, string}> raw => status, build, fixed */
    public static function versions(): iterable
    {
        yield 'affected upstream build' => ['8.3.12', 'vulnerable', 'upstream', '8.3.36'];
        yield 'fixed release' => ['8.3.36', 'ok', 'upstream', ''];
        yield 'build of deb.sury.org' => ['8.4.10-1+deb12u1+sury1', 'vulnerable', 'upstream', '8.4.27'];
        yield 'distribution build' => ['8.2.29-1~deb12u1', 'verify', 'distro', '8.2.35'];
        yield 'frozen in an LTS distribution' => ['8.3.6', 'verify', 'distro', '8.3.36'];
        yield 'no patch number' => ['8.4', 'verify', 'upstream', ''];
        yield 'branch without fix' => ['8.1.30', 'vulnerable', 'upstream', ''];
        yield 'branch not affected' => ['8.6.1', 'ok', 'upstream', ''];
        yield 'release candidate before the fix' => ['8.5.0RC1', 'vulnerable', 'upstream', '8.5.12'];
    }

    #[DataProvider('versions')]
    public function testStatusOfAVersion(string $raw, string $status, string $build, string $fixed): void
    {
        $result = PhpAdvisories::evaluate($raw, new \DateTimeImmutable('2026-11-12'));

        $this->assertSame([$status, $build, $fixed], [$result['status'], $result['build'], $result['fixed']]);

        if ('vulnerable' === $status) {
            $this->assertSame(['php2shell'], $result['affected']);
            $this->assertSame(['CVE-2026-109618', 'CVE-2026-109619'], $result['cves']);
        }
    }

    public function testWhatToUpdateTo(): void
    {
        $today = new \DateTimeImmutable('2026-11-12');

        $this->assertSame('8.3.36', PhpAdvisories::evaluate('8.3.12', $today)['target'], 'the own branch');
        $this->assertSame('8.3.36', PhpAdvisories::evaluate('7.4.33', $today)['target'], 'the lowest branch with support for another year');
        $this->assertSame(['8.2.35', '8.3.36', '8.4.27', '8.5.12'], PhpAdvisories::fixedReleases($today), 'no release candidates');
        $this->assertSame(['8.3.36', '8.4.27', '8.5.12'], PhpAdvisories::fixedReleases(new \DateTimeImmutable('2027-01-01')), '8.2 without support');
    }

    public function testAFixedReleaseWithoutSecuritySupportIsEol(): void
    {
        $this->assertSame('ok', PhpAdvisories::evaluate('8.2.35', new \DateTimeImmutable('2026-12-31'))['status']);
        $this->assertSame('eol', PhpAdvisories::evaluate('8.2.35', new \DateTimeImmutable('2027-01-01'))['status']);

        $old = PhpAdvisories::evaluate('7.4.33', new \DateTimeImmutable('2026-11-12'));
        $this->assertTrue($old['eol'], 'older than every listed branch');
        $this->assertSame('vulnerable', $old['status'], 'and without an official fix');
    }

    public function testNoVersion(): void
    {
        $this->assertNull(PhpAdvisories::evaluate('Apache', new \DateTimeImmutable('2026-11-12')));
        $this->assertNull(PhpAdvisories::evaluate('', new \DateTimeImmutable('2026-11-12')));
    }
}
