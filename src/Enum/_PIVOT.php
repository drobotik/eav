<?php

namespace Kuperwood\Eav\Enum;
use Kuperwood\Eav\Interface\DefineTableInterface;

enum _PIVOT implements DefineTableInterface
{
    case ID;
    case DOMAIN_ID;
    case SET_ID;
    case GROUP_ID;
    case ATTR_ID;

    public static function table() : string
    {
        return 'eav_pivot';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'pivot_id',
            self::DOMAIN_ID => _DOMAIN::ID->column(),
            self::SET_ID => _SET::ID->column(),
            self::GROUP_ID => _GROUP::ID->column(),
            self::ATTR_ID => _ATTR::ID->column()
        };
    }
}
