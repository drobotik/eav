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
use Doctrine\Migrations\AbstractMigration;
use Drobotik\Eav\Enum\ATTR_TYPE;

final class ValueDatetimeMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attribute datetime value table';
    }

    public function up(Schema $schema): void
    {
        ValueMigration::runUp($schema, ATTR_TYPE::DATETIME);
    }

    public function down(Schema $schema): void
    {
        ValueMigration::runDown($schema, ATTR_TYPE::DATETIME);
    }
}
