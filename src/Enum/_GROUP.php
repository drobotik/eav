<?php

namespace Kuperwood\Eav\Enum;
use Kuperwood\Eav\Interface\TableEnumInterface;

enum _GROUP implements TableEnumInterface
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
