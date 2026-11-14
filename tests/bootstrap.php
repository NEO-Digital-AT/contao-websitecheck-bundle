<?php

declare(strict_types=1);

/*
 * Autoloader of the tests: the package's own vendor/ (composer install), or a
 * shared one given in NEO_TEST_AUTOLOAD (several packages tested against one
 * installation). The test namespace is added here, because a shared vendor
 * does not read the autoload-dev section of this package.
 */
$autoload = getenv('NEO_TEST_AUTOLOAD') ?: __DIR__.'/../vendor/autoload.php';

if (!is_file($autoload)) {
    fwrite(STDERR, "No autoloader at $autoload: run composer install first.\n");
    exit(1);
}

$loader = require $autoload;
$loader->addPsr4('Neo\\WebsitecheckBundle\\Tests\\', __DIR__);
$loader->addPsr4('Neo\\WebsitecheckBundle\\', dirname(__DIR__).'/src', true);
