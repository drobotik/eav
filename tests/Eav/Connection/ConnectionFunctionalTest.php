<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Connection;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Exception\ConnectionException;
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
     * @covers \Drobotik\Eav\Database\Connection::get
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
     * @covers \Drobotik\Eav\Database\Connection::get
     */
    public function manual_connection()
    {
        $config = [
            'driver' => 'pdo_sqlite',
            'path' => dirname(__DIR__) . '/tests/test.sqlite'
        ];
        $connection = Connection::get($config);
        $this->assertEquals($config, $connection->getParams());
        $this->assertSame(Connection::get(), $connection);
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Database\Connection::reset
     * @covers \Drobotik\Eav\Database\Connection::get
     */
    public function reset()
    {
        $config = [
            'driver' => 'pdo_sqlite',
            'path' => dirname(__DIR__) . '/tests/test.sqlite'
        ];
        Connection::get($config);
        Connection::reset();
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage(ConnectionException::UNDEFINED);
        Connection::get();
    }
}
