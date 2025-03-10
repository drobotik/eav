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
use Drobotik\Eav\Enum\_DOMAIN;

final class DomainMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Domain table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_DOMAIN::table());
        $table->addColumn(_DOMAIN::ID, Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_DOMAIN::NAME, Types::STRING);
        $table->setPrimaryKey([_DOMAIN::ID]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_DOMAIN::table());
    }
}
