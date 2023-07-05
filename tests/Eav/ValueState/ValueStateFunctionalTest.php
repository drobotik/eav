<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueState;

use Drobotik\Eav\Value\ValueState;
use PHPUnit\Framework\TestCase;

class ValueStateFunctionalTest extends TestCase
{
    protected ValueState $state;

    public function setUp(): void
    {
        parent::setUp();
        $this->state = new ValueState();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueState::__construct(
     */
    public function constructor()
    {
        $mock = $this->getMockBuilder(ValueState::class)
            ->onlyMethods(['clear'])->getMock();
        $mock->expects($this->once())->method('clear');
        $mock->__construct();
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueState::get
     * @covers \Drobotik\Eav\Value\ValueState::set
     */
    public function getter_setter() {
        $this->assertEquals(null, $this->state->get());
        $this->state->set(1);
        $this->assertEquals(1, $this->state->get());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueState::isChanged
     */
    public function isChanged() {
        $this->assertFalse($this->state->isChanged());
        $this->state->set(1);
        $this->assertTrue($this->state->isChanged());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueState::clear
     */
    public function clear() {
        $this->state->set(1);
        $this->state->clear();
        $this->assertEquals(null, $this->state->get());
        $this->assertFalse($this->state->isChanged());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueState::set
     */
    public function setter_null() {
        $this->state->set(null);
        $this->assertEquals(null, $this->state->get());
        $this->assertTrue($this->state->isChanged());
    }
}
