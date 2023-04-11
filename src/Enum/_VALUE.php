<?php

declare(strict_types=1);

namespace Drobotik\Eav\Enum;

use Drobotik\Eav\Interface\DefineTableInterface;

enum _VALUE implements DefineTableInterface
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
