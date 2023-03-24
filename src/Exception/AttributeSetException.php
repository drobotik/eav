<?php

namespace Kuperwood\Eav\Exception;

use Exception;

class AttributeSetException extends Exception
{
    public const UNDEFINED_ATTRIBUTE = 'Undefined attribute: %s';

    /**
     * @throws AttributeSetException
     */
    public static function undefinedAttribute($name) {
        throw new static(sprintf(self::UNDEFINED_ATTRIBUTE, $name));
    }
}