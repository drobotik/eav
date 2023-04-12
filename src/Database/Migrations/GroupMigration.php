<?php

declare(strict_types=1);

namespace Drobotik\Eav\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Drobotik\Eav\Enum\_GROUP;

final class GroupMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attribute group table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_GROUP::table());
        $table->addColumn(_GROUP::ID->column(), Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_GROUP::SET_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_GROUP::NAME->column(), Types::STRING);
        $table->setPrimaryKey([_GROUP::ID->column()]);
        $table->addIndex([_GROUP::SET_ID->column()]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_GROUP::table());
    }
}
