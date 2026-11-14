<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Migration;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Column;

/**
 * Column names of a table for the takeover migrations. DBAL 4 deprecates
 * listTableColumns() in favour of introspectTableColumnsByUnquotedName()
 * (Contao 5.7 and 6.0); DBAL 3 (Contao 5.3 to 5.6) only has the former, so the
 * method that exists is used.
 */
final class TableColumns
{
    /**
     * @param AbstractSchemaManager<\Doctrine\DBAL\Platforms\AbstractPlatform> $schema
     *
     * @return list<string> lower case
     */
    public static function names(AbstractSchemaManager $schema, string $table): array
    {
        // @phpstan-ignore function.alreadyNarrowedType (always true with DBAL 4; DBAL 3 of Contao 5.3 to 5.6 lacks it)
        if (!method_exists($schema, 'introspectTableColumnsByUnquotedName')) {
            return array_map('strval', array_keys(array_change_key_case($schema->listTableColumns($table))));
        }

        return array_map(
            // @phpstan-ignore function.alreadyNarrowedType (getName() is deprecated where getObjectName() exists, older DBAL 4 releases lack it)
            static fn (Column $column): string => strtolower(method_exists($column, 'getObjectName') ? $column->getObjectName()->getIdentifier()->getValue() : $column->getName()),
            $schema->introspectTableColumnsByUnquotedName($table),
        );
    }

    /** @param AbstractSchemaManager<\Doctrine\DBAL\Platforms\AbstractPlatform> $schema */
    public static function has(AbstractSchemaManager $schema, string $table, string $column): bool
    {
        return $schema->tablesExist([$table]) && \in_array(strtolower($column), self::names($schema, $table), true);
    }
}
