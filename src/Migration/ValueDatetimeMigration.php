<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Kuperwood\Eav\Enum\AttributeTypeEnum;

final class ValueDatetimeMigration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Attribute datetime value table';
    }

    public function up(Schema $schema): void
    {
        ValueMigration::runUp($schema, AttributeTypeEnum::DATETIME);
    }

    public function down(Schema $schema): void
    {
        ValueMigration::runDown($schema, AttributeTypeEnum::DATETIME);
    }
}
