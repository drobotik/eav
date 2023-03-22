<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Kuperwood\Eav\Enum\AttributeTypeEnum;

final class ValueStringMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attribute string value table';
    }

    public function up(Schema $schema): void
    {
        ValueMigration::runUp($schema, AttributeTypeEnum::STRING);
    }

    public function down(Schema $schema): void
    {
        ValueMigration::runDown($schema, AttributeTypeEnum::STRING);
    }
}
