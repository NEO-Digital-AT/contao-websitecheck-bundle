<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Check;

use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\WebsitecheckBundle\Check\CheckRepository;
use Neo\WebsitecheckBundle\Check\SettingsRepository;
use Neo\WebsitecheckBundle\Settings;
use Neo\WebsitecheckBundle\Tests\Support\TestDatabase;
use Neo\WebsitecheckBundle\Tests\Support\TestSettings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** Every check is reached only with its random token; limits count per visitor and in total. */
#[CoversClass(CheckRepository::class)]
#[CoversClass(Settings::class)]
#[CoversClass(SettingsRepository::class)]
final class CheckRepositoryTest extends TestCase
{
    private Connection $db;

    private CheckRepository $repository;

    protected function setUp(): void
    {
        $this->db = TestDatabase::create('tl_neo_websitecheck', 'tl_neo_websitecheck_settings', 'tl_neo_access');
        TestDatabase::addMysqlFunctions($this->db);
        $this->repository = new CheckRepository($this->db, TestSettings::create());
    }

    public function testACheckIsFoundOnlyWithItsToken(): void
    {
        $id = $this->repository->create('example.com', 'https://www.example.com/', 'de-AT', 'hash-1', 3, 'www.example.com');
        $row = $this->repository->find($id);

        $this->assertMatchesRegularExpression('/^[a-f0-9]{40}$/', $row['token']);
        $this->assertSame([CheckRepository::STATUS_QUEUED, 'www.example.com', 3, []], [$row['status'], $row['site_host'], (int) $row['config_id'], $row['data']]);
        $this->assertSame($row, $this->repository->findWithToken($id, $row['token']));
        $this->assertNull($this->repository->findWithToken($id, ''));
        $this->assertNull($this->repository->findWithToken($id, substr($row['token'], 0, -1).'x'));
        $this->assertNull($this->repository->findWithToken($id + 1, $row['token']));
        $this->assertNotSame($row['token'], $this->repository->find($this->repository->create('example.com', 'https://www.example.com/', 'de', 'hash-1'))['token']);
    }

    public function testACachedResultIsCopiedWithoutTheDataOfTheFirstVisitor(): void
    {
        $source = $this->repository->create('example.com', 'https://www.example.com/', 'de', 'hash-1');
        $this->repository->update($source, ['status' => CheckRepository::STATUS_DONE, 'finished' => time(), 'grade' => 2, 'result' => ['grade' => 2], 'data' => ['psi' => ['home_mobile' => ['scores' => []]], 'consent' => ['contact' => ['time' => 1]], 'newsletterDone' => true, 'ownerNotified' => true, 'errors' => ['mail' => 'x', 'speed' => 'y']], 'firstname' => 'Max', 'email' => 'max.mustermann@example.com']);

        $cached = $this->repository->findRecentDone('https://www.example.com/', 24, 'de-AT');
        $this->assertSame($source, (int) $cached['id'], 'same language');
        $this->assertNull($this->repository->findRecentDone('https://www.example.com/', 24, 'en'), 'another language is written anew');
        $this->assertNull($this->repository->findRecentDone('https://www.example.com/', 0, 'de'), 'no cache');

        $copy = $this->repository->find($this->repository->createFromCache($cached, 'de-AT', 'hash-2', 0, 'www.example.com'));

        $this->assertSame([CheckRepository::STATUS_DONE, $source, 2, ['grade' => 2]], [$copy['status'], (int) $copy['source_id'], (int) $copy['grade'], $copy['result']]);
        $this->assertSame(['psi' => ['home_mobile' => ['scores' => []]], 'errors' => ['speed' => 'y']], $copy['data']);
        $this->assertSame('', (string) $copy['email']);
        $this->assertNotSame($cached['token'], $copy['token']);
    }

    public function testLimitsCountPerVisitorAndOnlyNewChecksInTotal(): void
    {
        $this->repository->create('example.com', 'https://www.example.com/', 'de', 'hash-1');
        $this->repository->create('example.org', 'https://example.org/', 'de', 'hash-1');
        $old = $this->repository->create('example.net', 'https://example.net/', 'de', 'hash-1');
        $this->db->update('tl_neo_websitecheck', ['created' => time() - 7200], ['id' => $old]);
        $other = $this->repository->create('example.com', 'https://www.example.com/', 'de', 'hash-2');
        $this->db->update('tl_neo_websitecheck', ['source_id' => $other], ['ip_hash' => 'hash-2']);

        $this->assertSame(2, $this->repository->countForIp('hash-1', 3600));
        $this->assertSame(3, $this->repository->countForIp('hash-1', 86400));
        $this->assertSame(2, $this->repository->countFresh(3600), 'copies of a cached result cost nothing');
    }

    public function testOneStepAtATime(): void
    {
        $id = $this->repository->create('example.com', 'https://www.example.com/', 'de', 'hash-1');

        $this->assertSame([$id], $this->repository->pendingIds());
        $this->assertTrue($this->repository->lock($id, 60));
        $this->assertFalse($this->repository->lock($id, 60), 'locked');
        $this->assertSame([], $this->repository->pendingIds());

        $this->repository->unlock($id);
        $this->repository->update($id, ['status' => CheckRepository::STATUS_DONE]);
        $this->assertFalse($this->repository->lock($id, 60), 'done');
    }

    public function testOldChecksAreDeleted(): void
    {
        $old = $this->repository->create('example.com', 'https://www.example.com/', 'de', 'hash-1');
        $this->db->update('tl_neo_websitecheck', ['created' => time() - 91 * 86400], ['id' => $old]);
        $kept = $this->repository->create('example.com', 'https://www.example.com/', 'de', 'hash-1');

        $this->assertSame(1, $this->repository->purge(90));
        $this->assertNull($this->repository->find($old));
        $this->assertNotNull($this->repository->find($kept));
        $this->assertSame(0, $this->repository->purge(90));
    }

    public function testLimitsFromTheBackEndOrTheConfiguration(): void
    {
        $settings = TestSettings::create(['limits' => ['checks_per_ip_per_hour' => 4, 'max_pages' => 6]]);
        $this->assertSame([4, 6], [$settings->checksPerIpPerHour(), $settings->maxPages()]);

        $this->db->insert('tl_neo_websitecheck_settings', ['tstamp' => 1, 'checks_per_ip_hour' => 2, 'max_pages' => 50]);
        $fromBackEnd = TestSettings::create(['limits' => ['checks_per_ip_per_hour' => 4]], new SettingsRepository($this->db));
        $this->assertSame([2, 8], [$fromBackEnd->checksPerIpPerHour(), $fromBackEnd->maxPages()], 'never more than eight pages');
    }

    public function testThePageSpeedKeyOfAccessKeysAndLicencesComesFirst(): void
    {
        $access = new AccessSettings($this->db, new SecretCipher('test-secret'));

        $this->assertSame('from-config', TestSettings::create(['pagespeed' => ['api_key' => ' from-config ']], null, $access)->pageSpeedKey());

        $access->store([Settings::PSI_KEY => 'from-back-end'], [Settings::PSI_KEY]);
        $this->assertSame('from-back-end', TestSettings::create(['pagespeed' => ['api_key' => 'from-config']], null, new AccessSettings($this->db, new SecretCipher('test-secret')))->pageSpeedKey());
    }
}
