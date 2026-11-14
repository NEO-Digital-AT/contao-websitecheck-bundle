<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Migration;

use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Ai\AiConnection;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\WebsitecheckBundle\Check\SettingsRepository;
use Neo\WebsitecheckBundle\Migration\AdoptAiConnectionMigration;
use Neo\WebsitecheckBundle\Migration\AdoptConfigKeysMigration;
use Neo\WebsitecheckBundle\Migration\AdoptPageSpeedKeyMigration;
use Neo\WebsitecheckBundle\Migration\TableColumns;
use Neo\WebsitecheckBundle\Settings;
use Neo\WebsitecheckBundle\Tests\Support\TestDatabase;
use Neo\WebsitecheckBundle\Tests\Support\TestSettings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** Keys and the model move once, from the settings record or config.yaml; nothing stored is overwritten. */
#[CoversClass(AdoptAiConnectionMigration::class)]
#[CoversClass(AdoptConfigKeysMigration::class)]
#[CoversClass(AdoptPageSpeedKeyMigration::class)]
#[CoversClass(TableColumns::class)]
final class MigrationsTest extends TestCase
{
    private Connection $db;

    private SecretCipher $cipher;

    protected function setUp(): void
    {
        $this->db = TestDatabase::create('tl_neo_access', 'tl_neo_websitecheck_settings');
        $this->cipher = new SecretCipher('test-secret');
    }

    public function testThePageSpeedKeyOfTheSettingsMovesOnce(): void
    {
        $this->db->insert('tl_neo_websitecheck_settings', ['tstamp' => 1, 'psi_api_key' => $this->cipher->encrypt('psi-stored&amp;1', Settings::CIPHER_SCOPE)]);
        $migration = $this->pageSpeed(['pagespeed' => ['api_key' => 'psi-config']]);

        $this->assertTrue($migration->shouldRun());
        $this->assertSame('Taken over the PageSpeed key from the settings of the website check.', $migration->run()->getMessage());
        $this->assertSame('psi-stored&1', $this->access()->secret(Settings::PSI_KEY), 'the stored key wins over the configured one');
        $this->assertFalse($this->pageSpeed(['pagespeed' => ['api_key' => 'psi-config']])->shouldRun());

        $this->access()->store([Settings::PSI_KEY => '']);
        $this->assertFalse($this->pageSpeed(['pagespeed' => ['api_key' => 'psi-config']])->shouldRun(), 'a key removed later does not come back');
    }

    public function testTheConfiguredPageSpeedKeyDoesNotOverwriteOne(): void
    {
        $this->access()->store([Settings::PSI_KEY => 'psi-access'], [Settings::PSI_KEY]);

        $this->assertSame('A PageSpeed key is already stored; the key from the bundle configuration was not taken over.', $this->pageSpeed(['pagespeed' => ['api_key' => 'psi-config']])->run()->getMessage());
        $this->assertSame('psi-access', $this->access()->secret(Settings::PSI_KEY));
        $this->assertFalse($this->pageSpeed()->shouldRun(), 'nothing configured');
    }

    public function testTheAiKeyAndRouteMoveToTheSharedConnection(): void
    {
        $this->db->insert('tl_neo_websitecheck_settings', ['tstamp' => 1, 'ai_api_key' => $this->cipher->encrypt('sk-stored', Settings::CIPHER_SCOPE), 'ai_base_url' => 'https://api.openai.com/v1']);
        $migration = new AdoptAiConnectionMigration($this->db, new AiConnection($this->access()), $this->cipher, TestSettings::create());

        $this->assertTrue($migration->shouldRun());
        $this->assertSame('Taken over the AI key and route "openai" from the settings of the website check.', $migration->run()->getMessage());

        $connection = new AiConnection($this->access());
        $this->assertSame(['openai', 'sk-stored'], [$connection->route(), $connection->apiKey()]);
        $this->assertFalse((new AdoptAiConnectionMigration($this->db, $connection, $this->cipher, TestSettings::create()))->shouldRun());
    }

    public function testTheModelOfTheConfigurationFillsAnEmptyField(): void
    {
        $settings = TestSettings::create(['ai' => ['api_key' => 'sk-config', 'model' => 'provider/model-a']], new SettingsRepository($this->db));
        $migration = new AdoptConfigKeysMigration($this->db, $settings);

        $this->assertTrue($migration->shouldRun());
        $this->assertSame('Taken over: ai_model', $migration->run()->getMessage());
        $this->assertSame('provider/model-a', $this->db->fetchOne('SELECT ai_model FROM tl_neo_websitecheck_settings'));
        $this->assertSame('', $this->db->fetchOne('SELECT ai_api_key FROM tl_neo_websitecheck_settings'), 'keys go to "Access keys and licences"');
        $this->assertFalse($migration->shouldRun());

        $this->db->update('tl_neo_websitecheck_settings', ['ai_model' => 'own/model'], ['ai_model' => 'provider/model-a']);
        $this->assertFalse((new AdoptConfigKeysMigration($this->db, TestSettings::create(['ai' => ['api_key' => 'sk-config', 'model' => 'other']], new SettingsRepository($this->db))))->shouldRun(), 'nothing stored is overwritten');
    }

    public function testAModelWithoutKeyIsNotWorthCopying(): void
    {
        $this->assertFalse((new AdoptConfigKeysMigration($this->db, TestSettings::create(['ai' => ['model' => 'provider/model-a']])))->shouldRun());
        $this->assertFalse((new AdoptConfigKeysMigration(TestDatabase::create(), TestSettings::create(['ai' => ['api_key' => 'k', 'model' => 'm']])))->shouldRun(), 'before the table exists');
    }

    private function access(): AccessSettings
    {
        return new AccessSettings($this->db, $this->cipher);
    }

    /** @param array<string, mixed> $config */
    private function pageSpeed(array $config = []): AdoptPageSpeedKeyMigration
    {
        return new AdoptPageSpeedKeyMigration($this->db, $this->access(), $this->cipher, TestSettings::create($config));
    }
}
