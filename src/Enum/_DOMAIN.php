<?php

namespace Kuperwood\Eav\Enum;
use Kuperwood\Eav\Interface\TableEnumInterface;

enum _DOMAIN implements TableEnumInterface
{
    CASE ID;
    CASE NAME;

    public static function table() : string
    {
        return 'eav_domain';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'domain_id',
            self::NAME => 'name',
        };
    }
}
