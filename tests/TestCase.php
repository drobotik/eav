<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Factory\EavFactory;
use Kuperwood\Eav\Traits\SingletonsTrait;
use Faker\Generator;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use SingletonsTrait;
    protected EavFactory $eavFactory;
    protected Generator $faker;
    protected function setUp() : void
    {
        parent::setUp();
        $schemaPath = dirname(__DIR__) . '/schema.sql';
        $dsn = getenv('GITHUB_ACTIONS') === 'true'
            ? 'mysql:host=127.0.0.1;port=3306;dbname=eav;charset=utf8mb4'
            : 'mysql:host=eav_db;port=3306;dbname=eav;charset=utf8mb4';
        $dbParams = [
            'dsn'      => $dsn,
            'user'     => 'root',
            'password' => 'root',
        ];
        $pdo = new \PDO($dbParams['dsn'], $dbParams['user'], $dbParams['password']);
        Connection::get($pdo);
        $sql = file_get_contents($schemaPath);
        $statements = array_filter(array_map('trim', explode(";", $sql)));
        $pdo = Connection::getNativeConnection();
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        $this->eavFactory = new EavFactory();
        $this->faker = \Faker\Factory::create();
    }

    protected function tearDown(): void
    {
        $schemaPath = dirname(__DIR__) . '/schema.sql';
        $sql = file_get_contents($schemaPath);
        $statements = array_filter(array_map('trim', explode(";", $sql)));
        $pdo = Connection::getNativeConnection();
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        Cleaner::run();
        parent::tearDown();
    }
}