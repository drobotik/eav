<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Kuperwood\Eav\Enum\_PIVOT;

final class PivotMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'EAV pivot table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_PIVOT::table());
        $table->addColumn(_PIVOT::ID->column(), Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_PIVOT::DOMAIN_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_PIVOT::SET_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_PIVOT::GROUP_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_PIVOT::ATTR_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->setPrimaryKey([_PIVOT::ID->column()]);
        $table->addIndex([_PIVOT::DOMAIN_ID->column()]);
        $table->addIndex([_PIVOT::SET_ID->column()]);
        $table->addIndex([_PIVOT::GROUP_ID->column()]);
        $table->addIndex([_PIVOT::ATTR_ID->column()]);
        $table->addUniqueIndex([
            _PIVOT::DOMAIN_ID->column(),
            _PIVOT::SET_ID->column(),
            _PIVOT::GROUP_ID->column(),
            _PIVOT::ATTR_ID->column()
        ]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_PIVOT::table());
    }
}
