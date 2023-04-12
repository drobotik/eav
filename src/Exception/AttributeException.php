<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Exception;

use Exception;

class AttributeException extends Exception
{
    public const UNEXPECTED_TYPE = 'Unexpected type: %s';

    /**
     * @throws AttributeException
     */
    public static function unexpectedType($type) {
        throw new static(sprintf(self::UNEXPECTED_TYPE, $type));
    }
}