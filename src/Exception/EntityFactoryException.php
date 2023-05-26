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

class EntityFactoryException extends Exception
{
    public const UNDEFINED_ATTRIBUTE_ARRAY = 'Attribute array must be provided!';

    /**
     * @throws EntityFactoryException
     */
    public static function undefinedAttributeArray()
    {
        throw new static(self::UNDEFINED_ATTRIBUTE_ARRAY);
    }
}