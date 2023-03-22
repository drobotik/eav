<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Kuperwood\Eav\Enum\AttributeTypeEnum;

final class ValueDecimalMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attribute decimal value table';
    }

    public function up(Schema $schema): void
    {
        ValueMigration::runUp($schema, AttributeTypeEnum::DECIMAL);
    }

    public function down(Schema $schema): void
    {
        ValueMigration::runDown($schema, AttributeTypeEnum::DECIMAL);
    }
}
