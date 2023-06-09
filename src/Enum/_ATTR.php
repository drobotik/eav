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
use Drobotik\Eav\Strategy;

enum _ATTR implements DefineTableInterface
{
    case ID;
    case DOMAIN_ID;
    case NAME;
    case TYPE;
    case STRATEGY;
    case SOURCE;
    case DEFAULT_VALUE;
    case DESCRIPTION;

    public static function table() : string
    {
        return 'eav_attributes';
    }

    public function column() : string
    {
        return match ($this) {
            self::ID => 'attribute_id',
            self::DOMAIN_ID => _DOMAIN::ID->column(),
            self::NAME => 'name',
            self::TYPE => 'type',
            self::STRATEGY => 'strategy',
            self::SOURCE => 'source',
            self::DEFAULT_VALUE => 'default_value',
            self::DESCRIPTION => 'description',
        };
    }

    public function default() : string|bool|null|ATTR_TYPE
    {
        return match ($this) {
            self::ID,
            self::NAME,
            self::DOMAIN_ID,
            self::SOURCE,
            self::DEFAULT_VALUE,
            self::DESCRIPTION=> null,
            self::TYPE => ATTR_TYPE::STRING->value(),
            self::STRATEGY => Strategy::class
        };
    }

    public static function bag() : array
    {
        $output = [];
        foreach (self::cases() as $case) {
            $output[$case->column()] = $case->default();
        }
        return $output;
    }
}
