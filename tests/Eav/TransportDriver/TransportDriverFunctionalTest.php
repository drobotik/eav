<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\TransportDriver;

use Drobotik\Eav\TransportDriver;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\TransportDriverFixture;

class TransportDriverFunctionalTest extends TestCase
{
    private TransportDriver $driver;

    public function setUp(): void
    {
        parent::setUp();
        $this->driver = new TransportDriverFixture();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\TransportDriver::getChunkSize
     * @covers \Drobotik\Eav\TransportDriver::setChunkSize
     */
    public function chunk_size()
    {
        $this->driver->setChunkSize(123);
        $this->assertEquals(123, $this->driver->getChunkSize());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\TransportDriver::getCursor
     * @covers \Drobotik\Eav\TransportDriver::setCursor
     */
    public function cursor()
    {
        $this->driver->setCursor(123);
        $this->assertEquals(123, $this->driver->getCursor());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\TransportDriver::getTotal
     * @covers \Drobotik\Eav\TransportDriver::setTotal
     */
    public function total()
    {
        $this->driver->setTotal(123);
        $this->assertEquals(123, $this->driver->getTotal());
    }
}