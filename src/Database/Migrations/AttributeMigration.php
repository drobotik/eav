<?php

declare(strict_types=1);

namespace Drobotik\Eav\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Drobotik\Eav\Enum\_ATTR;

final class AttributeMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attributes table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_ATTR::table());
        $table->addColumn(_ATTR::ID->column(), Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_ATTR::DOMAIN_ID->column(), Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_ATTR::NAME->column(), Types::STRING);
        $table->addColumn(_ATTR::TYPE->column(), Types::STRING);
        $table->addColumn(_ATTR::STRATEGY->column(), Types::STRING, ['notnull'=>false]);
        $table->addColumn(_ATTR::SOURCE->column(), Types::STRING, ['notnull'=>false]);
        $table->addColumn(_ATTR::DEFAULT_VALUE->column(), Types::STRING, ['notnull'=>false]);
        $table->addColumn(_ATTR::DESCRIPTION->column(), Types::STRING, ['notnull'=>false]);
        $table->setPrimaryKey([_ATTR::ID->column()]);
        $table->addIndex([_ATTR::DOMAIN_ID->column()]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_ATTR::table());
    }
}
