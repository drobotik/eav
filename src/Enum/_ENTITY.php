<?php

namespace Kuperwood\Eav\Enum;
use Kuperwood\Eav\Interface\DefineTableInterface;

enum _ENTITY implements DefineTableInterface
{
    CASE ID;
    CASE DOMAIN_ID;

    public static function table() : string
    {
        return 'eav_entities';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'entity_id',
            self::DOMAIN_ID => _DOMAIN::ID->column(),
        };
    }
}
