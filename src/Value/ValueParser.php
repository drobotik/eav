<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */

namespace Drobotik\Eav\Value;

use DateTime;
use Drobotik\Eav\Enum\ATTR_TYPE;
use InvalidArgumentException;

class ValueParser
{
    public function parseDecimal($value)
    {
        $scale = ATTR_TYPE::migrateOptions(ATTR_TYPE::DECIMAL)['scale'];
        return number_format($value, $scale, '.', '');
    }

    /**
     * @throws \Exception
     */
    public function parse($type, $value)
    {
        switch ($type) {
            case ATTR_TYPE::INTEGER:
            case ATTR_TYPE::STRING:
            case ATTR_TYPE::TEXT:
            case ATTR_TYPE::MANUAL:
                return $value;
            case ATTR_TYPE::DATETIME:
                return (new DateTime($value))->format('Y-m-d H:i:s');
            case ATTR_TYPE::DECIMAL:
                return $this->parseDecimal($value);
            default:
                throw new InvalidArgumentException("Unknown attribute type: " . $type);
        }
    }
}