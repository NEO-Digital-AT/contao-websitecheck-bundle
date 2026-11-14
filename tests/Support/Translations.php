<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Support;

use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

/** A translator with the real translation files of this bundle (translations/<domain>.<locale>.yaml). */
final class Translations
{
    public static function translator(): Translator
    {
        $translator = new Translator('en');
        $translator->addLoader('yaml', new YamlFileLoader());

        foreach (glob(__DIR__.'/../../translations/*.yaml') ?: [] as $file) {
            [$domain, $locale] = explode('.', basename($file, '.yaml'), 2);
            $translator->addResource('yaml', $file, $locale, $domain);
        }

        return $translator;
    }
}
