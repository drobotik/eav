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
        'Kuperwood\Dev\Migration\DomainMigration',
        'Kuperwood\Dev\Migration\EntityMigration',
        'Kuperwood\Dev\Migration\AttributeMigration',
        'Kuperwood\Dev\Migration\AttributeSetMigration',
        'Kuperwood\Dev\Migration\GroupMigration',
        'Kuperwood\Dev\Migration\PivotMigration',
        'Kuperwood\Dev\Migration\ValueDatetimeMigration',
        'Kuperwood\Dev\Migration\ValueDecimalMigration',
        'Kuperwood\Dev\Migration\ValueIntegerMigration',
        'Kuperwood\Dev\Migration\ValueStringMigration',
        'Kuperwood\Dev\Migration\ValueTextMigration',
    ],

    'migrations_paths' => [
        'Kuperwood\Eav\Migration' => './dev/Migration',
    ],

    'all_or_nothing' => true,
    'transactional' => true,
    'check_database_platform' => true,
    'organize_migrations' => 'none',
    'connection' => null,
    'em' => null,
];