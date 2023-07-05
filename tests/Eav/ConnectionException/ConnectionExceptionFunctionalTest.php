<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ConnectionException;

use Drobotik\Eav\Exception\ConnectionException;
use PHPUnit\Framework\TestCase;

class ConnectionExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\ConnectionException::undefined
     */
    public function undefined()
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage(ConnectionException::UNDEFINED);
        ConnectionException::undefined();
    }
}