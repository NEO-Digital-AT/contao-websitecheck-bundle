<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Neo\WebsitecheckBundle\Settings;

/**
 * The AI model lives in the settings record of this bundle; the keys live on
 * "Access keys and licences" (AdoptAiConnectionMigration, AdoptPageSpeedKeyMigration).
 * Installations that had the model in config.yaml / the environment get it
 * taken over once, so nobody has to type it again: only an empty back-end
 * field is filled, nothing stored there is overwritten. The configuration
 * stays the fallback.
 */
final class AdoptConfigKeysMigration extends AbstractMigration
{
    public function __construct(
        private readonly Connection $db,
        private readonly Settings $settings,
    ) {
    }

    public function getName(): string
    {
        return 'Website-Check: take over the AI model from the configuration';
    }

    public function shouldRun(): bool
    {
        return [] !== $this->pending();
    }

    public function run(): MigrationResult
    {
        $set = $this->pending();

        if ((int) $this->db->fetchOne('SELECT COUNT(*) FROM tl_neo_websitecheck_settings') < 1) {
            $this->db->insert('tl_neo_websitecheck_settings', ['tstamp' => time()]);
        }

        $id = (int) $this->db->fetchOne('SELECT id FROM tl_neo_websitecheck_settings ORDER BY id ASC LIMIT 1');
        $this->db->update('tl_neo_websitecheck_settings', $set + ['tstamp' => time()], ['id' => $id]);
        $this->settings->db()?->reset();

        return $this->createResult(true, 'Taken over: '.implode(', ', array_keys($set)));
    }

    /**
     * Configuration values whose back-end field is still empty. The AI model and
     * address only travel together with an AI key, the default address and model
     * of a bundle without any AI configured are not worth copying.
     *
     * @return array<string, string>
     */
    private function pending(): array
    {
        $schema = $this->db->createSchemaManager();

        if (!$schema->tablesExist(['tl_neo_websitecheck_settings'])) {
            return [];
        }

        $columns = TableColumns::names($schema, 'tl_neo_websitecheck_settings');

        foreach (array_keys($this->settings->configKeys()) as $column) {
            if (!\in_array(strtolower((string) $column), $columns, true)) {
                return [];
            }
        }

        $row = $this->db->fetchAssociative('SELECT * FROM tl_neo_websitecheck_settings ORDER BY id ASC LIMIT 1') ?: [];
        $config = $this->settings->configKeys();

        if ('' === $config['ai_api_key']) {
            unset($config['ai_model']);
        }

        // The keys go to "Access keys and licences" (AdoptAiConnectionMigration, AdoptPageSpeedKeyMigration).
        unset($config['ai_api_key'], $config['ai_base_url'], $config['psi_api_key']);

        $pending = [];

        foreach ($config as $column => $value) {
            if ('' !== $value && '' === trim((string) ($row[$column] ?? ''))) {
                $pending[$column] = $value;
            }
        }

        return $pending;
    }
}
