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

class ImportException extends Exception
{
    public const CONFIG_MISSED_ATTRIBUTES = 'Required config for attributes: %s';

    /**
     * @throws ImportException
     */
    public static function configMissedAttributes(array $attributes)
    {
        throw new static(sprintf(self::CONFIG_MISSED_ATTRIBUTES, implode(',', $attributes)));
    }
}