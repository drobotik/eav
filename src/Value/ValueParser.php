<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */

namespace Kuperwood\Eav\Value;

use DateTime;
use Kuperwood\Eav\Enum\ATTR_TYPE;
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
                return (int) $value;
            case ATTR_TYPE::STRING:
            case ATTR_TYPE::TEXT:
            case ATTR_TYPE::MANUAL:
                return $value;
            case ATTR_TYPE::DATETIME:
                return (new DateTime($value))->format('Y-m-d H:i:s');
            case ATTR_TYPE::DECIMAL:
                $valueStr = (string)$value;
                // Find the position of the decimal point
                $decimalPos = strpos($valueStr, '.');
                // If there is a decimal point and more than 6 decimals
                if ($decimalPos !== false && strlen($valueStr) > $decimalPos + 7) {
                    // Cut the value after 6 decimals
                    $valueStr = substr($valueStr, 0, $decimalPos + 7);
                }
                return rtrim(rtrim($valueStr, '0'), '.');
            default:
                throw new InvalidArgumentException("Unknown attribute type: " . $type);
        }
    }
}