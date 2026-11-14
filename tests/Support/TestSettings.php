<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Support;

use Neo\BasicBundle\Access\AccessSettings;
use Neo\BasicBundle\Ai\AiConnection;
use Neo\WebsitecheckBundle\Check\SettingsRepository;
use Neo\WebsitecheckBundle\DependencyInjection\Configuration;
use Neo\WebsitecheckBundle\Settings;
use Symfony\Component\Config\Definition\Processor;

/** Settings of the bundle with the defaults of its configuration tree (config.yaml without entries). */
final class TestSettings
{
    /** @param array<string, mixed> $config entries as in config.yaml under neo_websitecheck */
    public static function create(array $config = [], SettingsRepository|null $db = null, AccessSettings|null $access = null): Settings
    {
        $processed = (new Processor())->processConfiguration(new Configuration(), [['storage_dir' => sys_get_temp_dir().'/neo-websitecheck-tests'] + $config]);

        return new Settings($processed, sys_get_temp_dir(), $db, null, null !== $access ? new AiConnection($access) : null, $access);
    }
}
