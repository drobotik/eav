<?php

declare(strict_types=1);

namespace Tests\Unit\ValueState;

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
     * @covers ValueState::get
     */
    public function getter() {
        $this->assertEquals(null, $this->state->get());
    }
    /**
     * @test
     * @group functional
     * @covers ValueState::set
     */
    public function setter() {
        $this->state->set(1);
        $this->assertEquals(1, $this->state->get());
    }
    /**
     * @test
     * @group functional
     * @covers ValueState::isChanged
     */
    public function isChanged() {
        $this->assertFalse($this->state->isChanged());
        $this->state->set(1);
        $this->assertTrue($this->state->isChanged());
    }
    /**
     * @test
     * @group functional
     * @covers ValueState::clear
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
     * @covers ValueState::set
     */
    public function setter_null() {
        $this->state->set(null);
        $this->assertEquals(null, $this->state->get());
        $this->assertTrue($this->state->isChanged());
    }
}
