<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;

class ValueMigration
{
    public static function runUp(Schema $schema, $type) : Table
    {
        $table = $schema->createTable(sprintf(_VALUE::table(), ATTR_TYPE::getCase($type)));
        $table->addColumn(_VALUE::ID, Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_VALUE::DOMAIN_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_VALUE::ENTITY_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_VALUE::ATTRIBUTE_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_VALUE::VALUE, ATTR_TYPE::doctrineType($type), ATTR_TYPE::migrateOptions($type));
        $table->setPrimaryKey([_VALUE::ID]);
        $table->addIndex([_VALUE::DOMAIN_ID]);
        $table->addIndex([_VALUE::ENTITY_ID]);
        $table->addIndex([_VALUE::ATTRIBUTE_ID]);
        $table->addUniqueIndex([
            _VALUE::DOMAIN_ID,
            _VALUE::ENTITY_ID,
            _VALUE::ATTRIBUTE_ID
        ]);
        return $table;
    }

    public static function runDown(Schema $schema, $type) : void
    {
        $schema->dropTable(sprintf(_VALUE::table(), ATTR_TYPE::getCase($type)));
    }
}