<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Exception;

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