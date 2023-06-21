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

enum _ATTR_PROP implements DefineTableInterface
{
    case KEY;
    case ATTRIBUTE_KEY;
    case NAME;
    case VALUE;

    public static function table(): string
    {
        return "eav_attribute_properties";
    }

    public function column(): string
    {
        return match ($this) {
            self::KEY => "property_key",
            self::ATTRIBUTE_KEY => "attribute_key",
            self::NAME => "name",
            self::VALUE => "value"
        };
    }
}