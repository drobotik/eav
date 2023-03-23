<?php

declare(strict_types=1);

namespace Kuperwood\Dev\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Kuperwood\Eav\Enum\_DOMAIN;

final class DomainMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Domain table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_DOMAIN::table());
        $table->addColumn(_DOMAIN::ID->column(), Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_DOMAIN::NAME->column(), Types::STRING);
        $table->setPrimaryKey([_DOMAIN::ID->column()]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_DOMAIN::table());
    }
}
