<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Connection;

use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Exception\ConnectionException;
use PHPUnit\Framework\TestCase;

class ConnectionFunctionalTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Connection::reset();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Database\Connection::get
     */
    public function no_connection()
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage(ConnectionException::UNDEFINED);
        Connection::get();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Database\Connection::get
     */
    public function manual_connection()
    {
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
        // Retrieve the PDO instance from the Connection
        $connectionPdo = Connection::get();
        // Assert that the provided PDO instance is the same as the one returned by Connection::get()
        $this->assertSame($pdo, $connectionPdo, 'The PDO instances are not the same.');
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Database\Connection::reset
     * @covers \Kuperwood\Eav\Database\Connection::get
     */
    public function reset()
    {
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
        Connection::reset();
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage(ConnectionException::UNDEFINED);
        Connection::get();
    }
}
