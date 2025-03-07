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

class _ATTR_PROP implements DefineTableInterface
{
    public const KEY = 'property_key';
    public const ATTRIBUTE_KEY = 'attribute_key';
    public const NAME = 'name';
    public const VALUE = 'value';

    public static function table(): string
    {
        return "eav_attribute_properties";
    }

}