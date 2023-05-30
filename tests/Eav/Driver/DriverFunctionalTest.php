<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Driver;

use Drobotik\Eav\Driver;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\DriverFixture;

class DriverFunctionalTest extends TestCase
{
    private Driver $driver;

    public function setUp(): void
    {
        parent::setUp();
        $this->driver = new DriverFixture();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Driver::getChunkSize
     * @covers \Drobotik\Eav\Driver::setChunkSize
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
     * @covers \Drobotik\Eav\Driver::getCursor
     * @covers \Drobotik\Eav\Driver::setCursor
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
     * @covers \Drobotik\Eav\Driver::getTotal
     * @covers \Drobotik\Eav\Driver::setTotal
     */
    public function total()
    {
        $this->driver->setTotal(123);
        $this->assertEquals(123, $this->driver->getTotal());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Driver::setHeader
     * @covers \Drobotik\Eav\Driver::getHeader
     * @covers \Drobotik\Eav\Driver::isHeader
     */
    public function header()
    {
        $this->assertFalse($this->driver->isHeader());
        $this->driver->setHeader([123]);
        $this->assertTrue($this->driver->isHeader());
        $this->assertEquals([123], $this->driver->getHeader());
    }
}