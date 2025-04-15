<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Database;

use Kuperwood\Eav\Exception\ConnectionException;
use PDO;

class Connection
{
    protected static ?PDO $conn = null;

    public static function reset() : void
    {
        self::$conn = null;
    }

    /**
     * @throws ConnectionException
     */
    public static function get(PDO $pdo = null) : ?PDO
    {
        if ($pdo instanceof PDO) {
            self::$conn = $pdo;
            return self::$conn;
        } else if (self::$conn instanceof PDO) {
            return self::$conn;
        }
        ConnectionException::undefined();
    }

}