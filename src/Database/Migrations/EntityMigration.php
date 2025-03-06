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
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Drobotik\Eav\Enum\_ENTITY;

final class EntityMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Entity table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_ENTITY::table());
        $table->addColumn(_ENTITY::ID, Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_ENTITY::DOMAIN_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_ENTITY::ATTR_SET_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_ENTITY::SERVICE_KEY, Types::INTEGER, ['unsigned' => true, 'notnull' => false]);
        $table->setPrimaryKey([_ENTITY::ID]);
        $table->addIndex([_ENTITY::DOMAIN_ID]);
        $table->addIndex([_ENTITY::ATTR_SET_ID]);
        $table->addIndex([_ENTITY::SERVICE_KEY]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_ENTITY::table());
    }
}
