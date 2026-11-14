<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Support;

use Contao\Model;
use Contao\Model\Registry;

/**
 * Contao models with a fixed row, without a database: the models of this bundle
 * are final, and Contao's constructor reads the DCA through the container. The
 * row goes where Model::setRow() puts it; reading and setting properties then
 * work as in Contao.
 */
final class Models
{
    /**
     * @template T of Model
     *
     * @param class-string<T>      $class
     * @param array<string, mixed> $row
     *
     * @return T
     */
    public static function make(string $class, array $row): Model
    {
        $model = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
        (new \ReflectionProperty(Model::class, 'arrData'))->setValue($model, $row);

        // Column types are read from the database once; none are needed here.
        $types = new \ReflectionProperty(Model::class, 'arrColumnCastTypes');

        if ([] === (array) $types->getValue()) {
            $types->setValue(null, ['tl_neo_unit_tests' => []]);
        }

        return $model;
    }

    /**
     * A model that Model::findByPk() finds: Contao looks in its registry of loaded
     * models before it asks the database. Registry::getInstance()->reset() in tearDown().
     *
     * @template T of Model
     *
     * @param class-string<T>      $class
     * @param array<string, mixed> $row with "id"
     *
     * @return T
     */
    public static function register(string $class, array $row): Model
    {
        $model = self::make($class, $row);

        // Registry::register() reads the unique fields from the DCA through the container.
        $registry = new \ReflectionProperty(Registry::class, 'arrRegistry');
        $models = (array) $registry->getValue(Registry::getInstance());
        $models[$class::getTable()][$row['id']] = $model;
        $registry->setValue(Registry::getInstance(), $models);
        $GLOBALS['TL_MODELS'][$class::getTable()] = $class;

        return $model;
    }
}
