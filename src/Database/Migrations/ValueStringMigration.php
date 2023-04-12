<?php

declare(strict_types=1);

namespace Drobotik\Eav\Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Drobotik\Eav\Enum\ATTR_TYPE;

final class ValueStringMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attribute string value table';
    }

    public function up(Schema $schema): void
    {
        ValueMigration::runUp($schema, ATTR_TYPE::STRING);
    }

    public function down(Schema $schema): void
    {
        ValueMigration::runDown($schema, ATTR_TYPE::STRING);
    }
}
