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
        'Drobotik\Dev\Migration\DomainMigration',
        'Drobotik\Dev\Migration\EntityMigration',
        'Drobotik\Dev\Migration\AttributeMigration',
        'Drobotik\Dev\Migration\AttributeSetMigration',
        'Drobotik\Dev\Migration\GroupMigration',
        'Drobotik\Dev\Migration\PivotMigration',
        'Drobotik\Dev\Migration\ValueDatetimeMigration',
        'Drobotik\Dev\Migration\ValueDecimalMigration',
        'Drobotik\Dev\Migration\ValueIntegerMigration',
        'Drobotik\Dev\Migration\ValueStringMigration',
        'Drobotik\Dev\Migration\ValueTextMigration',
    ],

    'migrations_paths' => [
        'Drobotik\Eav\Migration' => './dev/Migration',
    ],

    'all_or_nothing' => true,
    'transactional' => true,
    'check_database_platform' => true,
    'organize_migrations' => 'none',
    'connection' => null,
    'em' => null,
];