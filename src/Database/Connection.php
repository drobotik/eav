<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Database;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;
use Drobotik\Eav\Exception\ConnectionException;
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
    public static function get(?PDO $pdo = null) : PDO
    {
        if(self::$conn instanceof PDO) {
            return self::$conn;
        }
        if($pdo instanceof PDO) {
            self::$conn = $pdo;
            return self::$conn;
        }
        ConnectionException::undefined();
    }

    public static function getNativeConnection() : PDO
    {
        return self::$conn;
    }

}