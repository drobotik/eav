<?php

namespace Kuperwood\Eav\Enum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Interface\TableEnumInterface;

enum _VALUE implements TableEnumInterface
{
    case ID;
    case DOMAIN_ID;
    case ENTITY_ID;
    case ATTRIBUTE_ID;
    case VALUE;

    public static function table() : string
    {
        return 'eav_value_%s';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'value_id',
            self::DOMAIN_ID => _DOMAIN::ID->column(),
            self::ENTITY_ID => _ENTITY::ID->column(),
            self::ATTRIBUTE_ID => _ATTR::ID->column(),
            self::VALUE => "value",
        };
    }


}
