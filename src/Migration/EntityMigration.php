<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;
use Kuperwood\Eav\Enum\_ENTITY;

final class EntityMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Entity table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_ENTITY::table());
        $table->addColumn(_ENTITY::ID->column(), Types::INTEGER , ['Autoincrement' => true]);
        $table->addColumn(_ENTITY::DOMAIN_ID->column(), Types::INTEGER);
        $table->setPrimaryKey([_ENTITY::ID->column()]);
        $table->addIndex([_ENTITY::DOMAIN_ID->column()]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_ENTITY::table());
    }
}
