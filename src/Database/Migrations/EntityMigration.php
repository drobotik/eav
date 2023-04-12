<?php

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
        $table->addColumn(_ENTITY::ID->column(), Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_ENTITY::DOMAIN_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_ENTITY::ATTR_SET_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->setPrimaryKey([_ENTITY::ID->column()]);
        $table->addIndex([_ENTITY::DOMAIN_ID->column()]);
        $table->addIndex([_ENTITY::ATTR_SET_ID->column()]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_ENTITY::table());
    }
}
