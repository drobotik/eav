<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Exception;

use Exception;

class AttributeException extends Exception
{
    public const UNDEFINED_NAME = 'Attribute name must be provided!';
    public const UNDEFINED_TYPE = 'Attribute type must be provided!';
    public const UNSUPPORTED_TYPE = 'Type is not supported: %s';

    /**
     * @throws AttributeException
     */
    public static function undefinedAttributeName()
    {
        throw new static(self::UNDEFINED_NAME);
    }

    /**
     * @throws AttributeException
     */
    public static function undefinedAttributeType()
    {
        throw new static(self::UNDEFINED_TYPE);
    }

    /**
     * @throws AttributeException
     */
    public static function unsupportedType($type)
    {
        throw new static(sprintf(self::UNSUPPORTED_TYPE, $type));
    }
}