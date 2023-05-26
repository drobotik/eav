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

enum _ENTITY implements DefineTableInterface
{
    case ID;
    case DOMAIN_ID;
    case ATTR_SET_ID;
    case SERVICE_KEY;

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
            self::SERVICE_KEY => "service_key"
        };
    }
}
