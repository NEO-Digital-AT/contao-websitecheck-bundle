<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Ai\AiConnection;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\WebsitecheckBundle\Settings;

/**
 * Address and key of the AI provider used to be stored in the settings of this
 * bundle (or in config.yaml); they now live once on "Access keys and licences"
 * (Basic bundle). An installation that has no key there yet gets the key and address
 * of the website check, once. Nothing stored in the settings is overwritten; the
 * old columns stay untouched. The model stays in the settings of this bundle.
 * The run is recorded (AiConnection::markAdopted), so it stays listed even when
 * another extension fills the connection in the same run.
 */
final class AdoptAiConnectionMigration extends AbstractMigration
{
    private const SOURCE = 'websitecheck';

    public function __construct(
        private readonly Connection $db,
        private readonly AiConnection $connection,
        private readonly SecretCipher $cipher,
        private readonly Settings $settings,
    ) {
    }

    public function getName(): string
    {
        return 'Website-Check: take over address and key of the AI provider into the shared AI connection';
    }

    public function shouldRun(): bool
    {
        return null !== $this->candidate();
    }

    public function run(): MigrationResult
    {
        $candidate = $this->candidate();

        if (null === $candidate) {
            return $this->createResult(true);
        }

        $this->connection->markAdopted(self::SOURCE);

        // Another extension may have filled the shared connection in the same run.
        if ($this->connection->hasKey()) {
            return $this->createResult(true, sprintf('The shared AI connection already has a key; the key from the %s was not taken over.', $candidate['source']));
        }

        $route = AiConnection::routeFor($candidate['base_url']);
        $this->connection->store($route['route'], $route['base_url'], $candidate['key']);

        return $this->createResult(true, sprintf('Taken over the AI key and route "%s" from the %s.', $route['route'], $candidate['source']));
    }

    /** @return array{key: string, base_url: string, source: string}|null */
    private function candidate(): array|null
    {
        // Not "already has a key": the answer must not change while other
        // takeover migrations run in the same process. On a new installation the
        // table of the connection only exists in the second pass.
        if (!$this->connection->isStorable() || $this->connection->isAdopted(self::SOURCE)) {
            return null;
        }

        $config = $this->settings->configKeys();
        $row = [];
        $schema = $this->db->createSchemaManager();

        if (TableColumns::has($schema, 'tl_neo_websitecheck_settings', 'ai_api_key')) {
            $row = $this->db->fetchAssociative('SELECT ai_api_key, ai_base_url FROM tl_neo_websitecheck_settings ORDER BY id ASC LIMIT 1') ?: [];
        }

        $stored = trim(html_entity_decode($this->cipher->decrypt((string) ($row['ai_api_key'] ?? ''), Settings::CIPHER_SCOPE), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ('' !== $stored) {
            return ['key' => $stored, 'base_url' => (string) ($row['ai_base_url'] ?? '') ?: $config['ai_base_url'], 'source' => 'settings of the website check'];
        }

        if ('' !== $config['ai_api_key']) {
            return ['key' => $config['ai_api_key'], 'base_url' => $config['ai_base_url'], 'source' => 'bundle configuration'];
        }

        return null;
    }
}
