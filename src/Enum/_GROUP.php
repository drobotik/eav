<?php

namespace Kuperwood\Eav\Enum;
use Kuperwood\Eav\Interface\DefineTableInterface;

enum _GROUP implements DefineTableInterface
{
    CASE ID;
    case SET_ID;
    CASE NAME;

    public static function table() : string
    {
        return 'eav_attribute_groups';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'group_id',
            self::SET_ID => _SET::ID->column(),
            self::NAME => "name",
        };
    }
}
