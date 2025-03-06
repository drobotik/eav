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
        $table->addColumn(_ATTR::ID, Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_ATTR::DOMAIN_ID, Types::INTEGER, ['unsigned' => true]);
        $table->addColumn(_ATTR::NAME, Types::STRING);
        $table->addColumn(_ATTR::TYPE, Types::STRING);
        $table->addColumn(_ATTR::STRATEGY, Types::STRING, ['notnull'=>false]);
        $table->addColumn(_ATTR::SOURCE, Types::STRING, ['notnull'=>false]);
        $table->addColumn(_ATTR::DEFAULT_VALUE, Types::STRING, ['notnull'=>false]);
        $table->addColumn(_ATTR::DESCRIPTION, Types::STRING, ['notnull'=>false]);
        $table->setPrimaryKey([_ATTR::ID]);
        $table->addIndex([_ATTR::DOMAIN_ID]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_ATTR::table());
    }
}
