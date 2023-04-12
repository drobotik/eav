<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Enum;

enum ATTR_FACTORY
{
    case ATTRIBUTE;
    case GROUP;
    case VALUE;

    public function field(): string
    {
        return match ($this) {
            self::ATTRIBUTE => "attribute",
            self::GROUP => "group",
            self::VALUE => "value"
        };
    }
}