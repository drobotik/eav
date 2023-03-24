<?php

namespace Tests\Unit\Value;

use Kuperwood\Eav\Value\ValueState;
use PHPUnit\Framework\TestCase;

class ValueStateTest extends TestCase
{
    protected ValueState $state;

    public function setUp(): void
    {
        parent::setUp();
        $this->state = new ValueState();
    }

    /** @test */
    public function getter() {
        $this->assertEquals(null, $this->state->get());
    }

    /** @test */
    public function setter() {
        $this->state->set(1);
        $this->assertEquals(1, $this->state->get());
    }

    /** @test */
    public function isChanged() {
        $this->assertFalse($this->state->isChanged());
        $this->state->set(1);
        $this->assertTrue($this->state->isChanged());
    }

    /** @test */
    public function clear() {
        $this->state->set(1);
        $this->state->clear();
        $this->assertEquals(null, $this->state->get());
        $this->assertFalse($this->state->isChanged());
    }

    /** @test */
    public function setter_null() {
        $this->state->set(null);
        $this->assertEquals(null, $this->state->get());
        $this->assertTrue($this->state->isChanged());
    }
}
