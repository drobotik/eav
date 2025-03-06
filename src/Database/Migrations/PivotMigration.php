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
use Drobotik\Eav\Enum\_PIVOT;

final class PivotMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'EAV pivot table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_PIVOT::table());
        $table->addColumn(_PIVOT::ID, Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_PIVOT::DOMAIN_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_PIVOT::SET_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_PIVOT::GROUP_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_PIVOT::ATTR_ID, Types::INTEGER, ['unsigned' => true]);
        $table->setPrimaryKey([_PIVOT::ID]);
        $table->addIndex([_PIVOT::DOMAIN_ID]);
        $table->addIndex([_PIVOT::SET_ID]);
        $table->addIndex([_PIVOT::GROUP_ID]);
        $table->addIndex([_PIVOT::ATTR_ID]);
        $table->addUniqueIndex([
            _PIVOT::DOMAIN_ID,
            _PIVOT::SET_ID,
            _PIVOT::GROUP_ID,
            _PIVOT::ATTR_ID
        ]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_PIVOT::table());
    }
}
