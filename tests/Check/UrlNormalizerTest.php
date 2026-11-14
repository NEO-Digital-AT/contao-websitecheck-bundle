<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Check;

use Neo\WebsitecheckBundle\Check\UrlNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** Visitors choose the address: only public websites are checked (SSRF guard). */
#[CoversClass(UrlNormalizer::class)]
final class UrlNormalizerTest extends TestCase
{
    /** @var array<string, list<array<string, string>>> host => DNS records */
    private const DNS = [
        'example.com' => [['ip' => '93.184.215.14']],
        'www.example.com' => [['ip' => '93.184.215.14'], ['ipv6' => '2606:2800:21f:cb07:6820:80da:af6b:8b2c']],
        'xn--mller-kva.example' => [['ip' => '93.184.215.15']],
        'internal.example.com' => [['ip' => '10.0.0.8']],
        'mixed.example.com' => [['ip' => '93.184.215.14'], ['ip' => '127.0.0.1']],
        'v6local.example.com' => [['ipv6' => '::1']],
        'metadata.example.com' => [['ip' => '169.254.169.254']],
    ];

    /** @return iterable<string, array{string, array{domain: string, url: string}}> */
    public static function accepted(): iterable
    {
        yield 'bare domain' => ['Example.com', ['domain' => 'example.com', 'url' => 'https://example.com/']];
        yield 'with www and path' => [' https://www.example.com/shop ', ['domain' => 'example.com', 'url' => 'https://www.example.com/shop']];
        yield 'http becomes https' => ['http://www.example.com', ['domain' => 'example.com', 'url' => 'https://www.example.com/']];
        yield 'umlaut domain' => ['müller.example', ['domain' => 'xn--mller-kva.example', 'url' => 'https://xn--mller-kva.example/']];
        yield 'spaces inside' => ['www. example .com', ['domain' => 'example.com', 'url' => 'https://www.example.com/']];
    }

    /** @param array{domain: string, url: string} $expected */
    #[DataProvider('accepted')]
    public function testAPublicWebsiteBecomesAnHttpsAddress(string $input, array $expected): void
    {
        $this->assertSame($expected, $this->normalizer()->normalize($input));
    }

    /** @return iterable<string, array{string}> */
    public static function refused(): iterable
    {
        yield 'empty' => [''];
        yield 'bare word' => ['intranet'];
        yield 'localhost' => ['http://localhost/admin'];
        yield 'local network name' => ['printer.local'];
        yield 'ip literal' => ['http://192.168.1.1/'];
        yield 'ipv6 literal' => ['http://[::1]/'];
        yield 'credentials' => ['https://user:secret@example.com/'];
        yield 'not resolvable' => ['shop.invalid'];
        yield 'resolves to a private network' => ['internal.example.com'];
        yield 'one record private' => ['mixed.example.com'];
        yield 'ipv6 loopback' => ['v6local.example.com'];
        yield 'cloud metadata' => ['metadata.example.com'];
        yield 'leading hyphen' => ['-example.com'];
        yield 'too long' => [str_repeat('a', 250).'.com'];
        yield 'script' => ['javascript:alert(1)'];
    }

    #[DataProvider('refused')]
    public function testEverythingElseIsRefused(string $input): void
    {
        $this->assertNull($this->normalizer()->normalize($input));
    }

    private function normalizer(): UrlNormalizer
    {
        return new UrlNormalizer(static fn (string $host): array|false => self::DNS[$host] ?? false);
    }
}
