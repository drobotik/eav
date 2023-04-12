<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Enum;

use Drobotik\Eav\Interface\DefineTableInterface;

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
