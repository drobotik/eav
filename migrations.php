<?php
return [
    'table_storage' => [
        'table_name' => 'eav_migrations',
        'version_column_name' => 'versionfaf',
        'version_column_length' => 191,
        'executed_at_column_name' => 'executed_at',
        'execution_time_column_name' => 'execution_time',
    ],

    'migrations' => [
        'Drobotik\Eav\Database\Migrations\DomainMigration',
        'Drobotik\Eav\Database\Migrations\EntityMigration',
        'Drobotik\Eav\Database\Migrations\AttributeMigration',
        'Drobotik\Eav\Database\Migrations\AttributeSetMigration',
        'Drobotik\Eav\Database\Migrations\GroupMigration',
        'Drobotik\Eav\Database\Migrations\PivotMigration',
        'Drobotik\Eav\Database\Migrations\ValueDatetimeMigration',
        'Drobotik\Eav\Database\Migrations\ValueDecimalMigration',
        'Drobotik\Eav\Database\Migrations\ValueIntegerMigration',
        'Drobotik\Eav\Database\Migrations\ValueStringMigration',
        'Drobotik\Eav\Database\Migrations\ValueTextMigration',
    ],

    'migrations_paths' => [
        'Drobotik\Eav\Database\Migrations' => './src/Database/Migrations',
    ],

    'all_or_nothing' => true,
    'transactional' => true,
    'check_database_platform' => true,
    'organize_migrations' => 'none',
    'connection' => null,
    'em' => null,
];