<?php

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