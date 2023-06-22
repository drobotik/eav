<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */

namespace Drobotik\Eav\Value;

use Drobotik\Eav\Enum\ATTR_TYPE;

class ValueParser
{
    public function parseDecimal($value) : string|float|int
    {
        $scale = ATTR_TYPE::DECIMAL->migrateOptions()['scale'];
        return number_format($value, $scale, '.', '');
    }

    public function parse(ATTR_TYPE $type, $value) : mixed
    {
        return match($type)
        {
            ATTR_TYPE::INTEGER, ATTR_TYPE::DATETIME, ATTR_TYPE::STRING, ATTR_TYPE::TEXT, ATTR_TYPE::MANUAL => $value,
            ATTR_TYPE::DECIMAL => $this->parseDecimal($value),
        };
    }
}