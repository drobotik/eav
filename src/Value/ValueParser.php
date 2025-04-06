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
                return rtrim(rtrim($value, '0'), '.');
            default:
                throw new InvalidArgumentException("Unknown attribute type: " . $type);
        }
    }
}