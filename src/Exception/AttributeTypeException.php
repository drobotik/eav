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

class AttributeTypeException extends Exception
{
    public const UNSUPPORTED_TYPE = 'Type is not supported!';

    /**
     * @throws AttributeTypeException
     */
    public static function unsupportedType()
    {
        throw new static(self::UNSUPPORTED_TYPE);
    }
}