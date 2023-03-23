<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Kuperwood\Eav\Enum\ATTR_TYPE;

final class ValueDecimalMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attribute decimal value table';
    }

    public function up(Schema $schema): void
    {
        ValueMigration::runUp($schema, ATTR_TYPE::DECIMAL);
    }

    public function down(Schema $schema): void
    {
        ValueMigration::runDown($schema, ATTR_TYPE::DECIMAL);
    }
}
