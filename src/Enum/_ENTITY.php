<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Enum;

use Kuperwood\Eav\Interface\DefineTableInterface;

enum _ENTITY implements DefineTableInterface
{
    case ID;
    case DOMAIN_ID;
    case ATTR_SET_ID;

    public static function table() : string
    {
        return 'eav_entities';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'entity_id',
            self::DOMAIN_ID => _DOMAIN::ID->column(),
            self::ATTR_SET_ID => _SET::ID->column(),
        };
    }
}
