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

class Connection
{
    protected static DBALConnection|null $conn = null;

    public static function getConnection(array $params = null) : DBALConnection
    {
        if(!is_null(self::$conn)) {
            return self::$conn;
        }
        if(PHP_SAPI == 'cli' && str_contains($_SERVER['argv'][0], 'phpunit'))
        {
            $params = [
                'driver' => 'pdo_sqlite',
                'path' => dirname(__DIR__, 2) . '/tests/test.sqlite'
            ];
        }
        else {
            $config = [
                'driver' => "pdo_mysql",
                'host' => "mysql",
                'port' => "3306",
                'dbname' => "eav",
                'user' => "eav",
                'password' => "eav"
            ];
            $params = is_null($params)
                ? $config
                : $params;
        }
        self::$conn = DriverManager::getConnection($params);
        return self::$conn;
    }
}