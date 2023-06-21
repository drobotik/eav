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
use Drobotik\Eav\Enum\_ATTR_PROP;

final class AttributePropMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attribute properties table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable(_ATTR_PROP::table());
        $table->addColumn(_ATTR_PROP::KEY->column(), Types::INTEGER , ['Autoincrement' => true, 'unsigned' => true]);
        $table->addColumn(_ATTR_PROP::ATTRIBUTE_KEY->column(), Types::INTEGER);
        $table->addColumn(_ATTR_PROP::NAME->column(), Types::STRING);
        $table->addColumn(_ATTR_PROP::VALUE->column(), Types::STRING);
        $table->setPrimaryKey([_ATTR_PROP::KEY->column()]);
        $table->addIndex([_ATTR_PROP::ATTRIBUTE_KEY->column()]);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable(_ATTR_PROP::table());
    }
}
