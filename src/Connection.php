<?php

namespace Kuperwood\Eav;
use Doctrine\DBAL\DriverManager;
class Connection
{
    protected static \Doctrine\DBAL\Connection|null $conn = null;

    public static function getConnection(array $params = null)
    {
        if(!is_null(self::$conn)) {
            return self::$conn;
        }

        $fromEnv = [
            'driver' => 'pdo_mysql',
            'dbname' => 'eav',
            'user' => 'eav',
            'password' => 'eav',
            'host' => 'mysql'
        ];

        $params = is_null($params)
            ? $fromEnv
            : array_merge($fromEnv, $params);

        if(PHP_SAPI == 'cli') {
            if (
                str_contains($_SERVER['argv'][0], 'phpunit')
            ) {
                $params = [
                    'driver' => 'pdo_sqlite',
                    'path' => __DIR__ . '/../tests/test.sqlite'
                ];
            }
        }

        self::$conn = DriverManager::getConnection($params);
        return self::$conn;
    }



}