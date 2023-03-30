<?php

namespace Kuperwood\Eav\Enum;
use Kuperwood\Eav\Interface\DefineTableInterface;

enum _DOMAIN implements DefineTableInterface
{
    case ID;
    case NAME;

    public static function table() : string
    {
        return 'eav_domains';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'domain_id',
            self::NAME => 'name',
        };
    }
}
