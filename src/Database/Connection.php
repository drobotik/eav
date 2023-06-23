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

class Connection
{
    protected static DBALConnection|null $conn = null;

    public static function reset() : void
    {
        self::$conn = null;
    }

    public static function get(array $params = null) : DBALConnection
    {
        if(!is_null($params)) {
            self::$conn = DriverManager::getConnection($params);
            return self::$conn;
        }
        if(!is_null(self::$conn)) {
            return self::$conn;
        }
        ConnectionException::undefined();
    }

}