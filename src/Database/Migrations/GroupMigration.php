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
        $table->addColumn(_GROUP::ID, Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_GROUP::SET_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_GROUP::NAME, Types::STRING);
        $table->setPrimaryKey([_GROUP::ID]);
        $table->addIndex([_GROUP::SET_ID]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_GROUP::table());
    }
}
