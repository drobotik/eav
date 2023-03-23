<?php

namespace Kuperwood\Eav\Enum;
use Kuperwood\Eav\Interface\DefineTableInterface;

enum _SET implements DefineTableInterface
{
    CASE ID;
    CASE NAME;

    public static function table() : string
    {
        return 'eav_attribute_sets';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'set_id',
            self::NAME => "name",
        };
    }
}
