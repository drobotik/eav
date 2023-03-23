<?php
return [
    'table_storage' => [
        'table_name' => 'migrations',
        'version_column_name' => 'versionfaf',
        'version_column_length' => 191,
        'executed_at_column_name' => 'executed_at',
        'execution_time_column_name' => 'execution_time',
    ],

    'migrations' => [
        'Dev\Migration\DomainMigration',
        'Dev\Migration\EntityMigration',
        'Dev\Migration\AttributeMigration',
        'Dev\Migration\SetMigration',
        'Dev\Migration\GroupMigration',
        'Dev\Migration\PivotMigration',
        'Dev\Migration\ValueDatetimeMigration',
        'Dev\Migration\ValueDecimalMigration',
        'Dev\Migration\ValueIntegerMigration',
        'Dev\Migration\ValueStringMigration',
        'Dev\Migration\ValueTextMigration',
    ],

    'migrations_paths' => [
        'Kuperwood\Eav\Migration' => './src/Migration',
    ],

    'all_or_nothing' => true,
    'transactional' => true,
    'check_database_platform' => true,
    'organize_migrations' => 'none',
    'connection' => null,
    'em' => null,
];