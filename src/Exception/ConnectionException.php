<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Exception;

class ConnectionException extends \Exception
{
    public const UNDEFINED = "Undefined database connection!";

    /**
     * @throws ConnectionException
     */
    public static function undefined() {
        throw new static(self::UNDEFINED);
    }
}