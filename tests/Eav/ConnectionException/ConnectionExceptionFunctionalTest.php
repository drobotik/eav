<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ConnectionException;

use Kuperwood\Eav\Exception\ConnectionException;
use PHPUnit\Framework\TestCase;

class ConnectionExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Exception\ConnectionException::undefined
     */
    public function undefined()
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage(ConnectionException::UNDEFINED);
        ConnectionException::undefined();
    }
}