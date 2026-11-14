<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Support;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Neo\BasicBundle\NeoBasicBundle;

/**
 * An SQLite database in memory with the tables of this bundle and of the Basic
 * bundle, built from the "sql" definitions of their DCA files (Basic first, then
 * the fields this bundle adds): the tests run the real queries against the real
 * column names and defaults.
 */
final class TestDatabase
{
    public static function create(string ...$tables): Connection
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);

        foreach ($tables as $table) {
            $db->executeStatement(self::createStatement($table));
        }

        return $db;
    }

    /**
     * MySQL functions the queries of this bundle use, for SQLite (same results for the values tested).
     */
    public static function addMysqlFunctions(Connection $db): void
    {
        $pdo = $db->getNativeConnection();
        $substringIndex = static function (string|null $value, string $delimiter, int $count): string {
            $parts = explode($delimiter, (string) $value);

            return implode($delimiter, $count >= 0 ? \array_slice($parts, 0, $count) : \array_slice($parts, $count));
        };

        // PHP 8.4 added Pdo\Sqlite::createFunction(); PDO::sqliteCreateFunction() is the older name.
        method_exists($pdo, 'createFunction') ? $pdo->createFunction('SUBSTRING_INDEX', $substringIndex, 3) : $pdo->sqliteCreateFunction('SUBSTRING_INDEX', $substringIndex, 3);
    }

    /**
     * The columns of tl_page the bundle reads (Contao's table, not part of this bundle).
     *
     * @param list<array<string, mixed>> $rows
     */
    public static function addPages(Connection $db, array $rows): void
    {
        $db->executeStatement("CREATE TABLE IF NOT EXISTS tl_page (id INTEGER PRIMARY KEY AUTOINCREMENT, pid int NOT NULL DEFAULT 0, sorting int NOT NULL DEFAULT 0, tstamp int NOT NULL DEFAULT 0, title varchar(255) NOT NULL DEFAULT '', pageTitle varchar(255) NOT NULL DEFAULT '', description text NULL, robots varchar(32) NOT NULL DEFAULT '', type varchar(64) NOT NULL DEFAULT 'regular', language varchar(64) NOT NULL DEFAULT '', dns varchar(255) NOT NULL DEFAULT '', published char(1) NOT NULL DEFAULT '1', protected char(1) NOT NULL DEFAULT '', start varchar(10) NOT NULL DEFAULT '', stop varchar(10) NOT NULL DEFAULT '')");

        foreach ($rows as $row) {
            $db->insert('tl_page', $row);
        }
    }

    private static function createStatement(string $table): string
    {
        $saved = [$GLOBALS['TL_DCA'] ?? null, $GLOBALS['TL_LANG'] ?? null];
        $basic = \dirname((string) (new \ReflectionClass(NeoBasicBundle::class))->getFileName(), 2).'/contao/dca/';

        if (is_file($basic.$table.'.php')) {
            include $basic.$table.'.php';
        }

        if (is_file(__DIR__.'/../../contao/dca/'.$table.'.php')) {
            try {
                include __DIR__.'/../../contao/dca/'.$table.'.php';
            } catch (\Throwable) {
                // The licence and terms fields this bundle adds to a table of the Basic bundle need
                // Contao's container; its own columns come before them.
            }
        }

        $fields = $GLOBALS['TL_DCA'][$table]['fields'] ?? [];
        [$GLOBALS['TL_DCA'], $GLOBALS['TL_LANG']] = $saved;

        $columns = [];

        foreach ($fields as $name => $field) {
            if (isset($field['sql'])) {
                $columns[] = '"'.$name.'" '.self::columnType($field['sql']);
            }
        }

        if ([] === $columns) {
            throw new \LogicException(sprintf('No DCA with columns for %s.', $table));
        }

        return sprintf('CREATE TABLE "%s" (%s)', $table, implode(', ', $columns));
    }

    /** @param string|array<string, mixed> $sql MySQL column definition or Doctrine column options */
    private static function columnType(string|array $sql): string
    {
        if (\is_array($sql)) {
            $type = match ($sql['type'] ?? 'string') {
                'string' => 'varchar('.($sql['length'] ?? 255).')',
                'integer', 'smallint', 'bigint', 'boolean' => 'int',
                'binary', 'blob' => 'blob',
                default => 'text',
            };
            $null = ($sql['notnull'] ?? true) ? ' NOT NULL' : ' NULL';
            $default = \array_key_exists('default', $sql) && null !== $sql['default'] ? " DEFAULT '".str_replace("'", "''", (string) $sql['default'])."'" : '';

            return $type.$null.$default;
        }

        if (str_contains($sql, 'auto_increment')) {
            return 'INTEGER PRIMARY KEY AUTOINCREMENT';
        }

        // SQLite knows no binary types (Doctrine could not read the schema back).
        return trim((string) preg_replace(['/\bunsigned\b/i', '/\b(COLLATE|CHARACTER SET)\s+\S+/i', '/^(var)?binary\(\d+\)|^(tiny|medium|long)blob\b/i'], ['', '', 'blob'], $sql));
    }
}
