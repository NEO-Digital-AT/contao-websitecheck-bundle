<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;
use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Security\SecretCipher;
use Neo\WebsitecheckBundle\Settings;

/**
 * The PageSpeed key used to be stored in the settings of this bundle (or in
 * config.yaml); it now lives on "Access keys and licences". An installation
 * without a key there gets the stored key, else the configured one, once. A key
 * already stored there is not overwritten; the old column stays untouched. The
 * run is recorded, so a key removed later does not come back.
 */
final class AdoptPageSpeedKeyMigration extends AbstractMigration
{
    private const SOURCE = 'websitecheck_psi';

    public function __construct(
        private readonly Connection $db,
        private readonly AccessSettings $access,
        private readonly SecretCipher $cipher,
        private readonly Settings $settings,
    ) {
    }

    public function getName(): string
    {
        return 'Website-Check: take over the PageSpeed key into "Access keys and licences"';
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

        $this->access->markAdopted(self::SOURCE);

        if ($this->access->hasValue(Settings::PSI_KEY)) {
            return $this->createResult(true, sprintf('A PageSpeed key is already stored; the key from the %s was not taken over.', $candidate['source']));
        }

        $this->access->store([Settings::PSI_KEY => $candidate['key']], [Settings::PSI_KEY]);

        return $this->createResult(true, sprintf('Taken over the PageSpeed key from the %s.', $candidate['source']));
    }

    /** @return array{key: string, source: string}|null */
    private function candidate(): array|null
    {
        if (!$this->access->hasColumns('adopted', Settings::PSI_KEY) || $this->access->isAdopted(self::SOURCE)) {
            return null;
        }

        $schema = $this->db->createSchemaManager();

        if (TableColumns::has($schema, 'tl_neo_websitecheck_settings', 'psi_api_key')) {
            $stored = (string) $this->db->fetchOne('SELECT psi_api_key FROM tl_neo_websitecheck_settings ORDER BY id ASC LIMIT 1');
            $key = trim(html_entity_decode($this->cipher->decrypt($stored, Settings::CIPHER_SCOPE), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            if ('' !== $key) {
                return ['key' => $key, 'source' => 'settings of the website check'];
            }
        }

        $configured = $this->settings->configKeys()['psi_api_key'];

        return '' !== $configured ? ['key' => $configured, 'source' => 'bundle configuration'] : null;
    }
}
