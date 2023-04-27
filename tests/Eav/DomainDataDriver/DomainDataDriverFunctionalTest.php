<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\DomainDataDriver;

use Drobotik\Eav\DomainDataDriver;
use Tests\Fixtures\DomainDataDriverFixture;
use Tests\TestCase;

class DomainDataDriverFunctionalTest extends TestCase
{
    private DomainDataDriver $driver;

    public function setUp(): void
    {
        parent::setUp();
        $this->driver = new class extends DomainDataDriverFixture {};
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\DomainDataDriver::getData
     * @covers \Drobotik\Eav\DomainDataDriver::setData
     * @covers \Drobotik\Eav\DomainDataDriver::hasField
     * @covers \Drobotik\Eav\DomainDataDriver::getField
     */
    public function config()
    {
        $this->assertEquals([], $this->driver->getData());
        $data = ['foo' => 'bar'];
        $this->assertFalse($this->driver->hasField('foo'));
        $this->driver->setData($data);
        $this->assertEquals($data, $this->driver->getData());
        $this->assertEquals('bar', $this->driver->getField('foo'));
    }
}
