<?php

namespace Tests\Unit\Value;

use Kuperwood\Eav\ValueManager;
use PHPUnit\Framework\TestCase;

class ValueManagerTest extends TestCase
{
    protected ValueManager $value;

    public function setUp(): void
    {
        parent::setUp();
        $this->value = new ValueManager();
    }

    /** @test  */
    public function key() {
        $this->value->setKey(1);
        $this->assertEquals(1, $this->value->getKey());
    }

    /** @test  */
    public function get_runtime_value() {
        $this->assertEquals(null, $this->value->getRuntime());
    }

    /** @test  */
    public function set_runtime_value() {
        $this->value->setRuntime(1);
        $this->assertEquals(1, $this->value->getRuntime());
        $this->value->setRuntime('test');
        $this->assertEquals('test', $this->value->getRuntime());
    }

    /** @test  */
    public function is_runtime_value() {
        $this->assertFalse($this->value->isRuntime());
        $this->value->setRuntime(1);
        $this->assertTrue($this->value->isRuntime());
    }

    /** @test  */
    public function clear_runtime_value() {
        $this->value->setRuntime(1);
        $this->value->clearRuntime();
        $this->assertFalse($this->value->isRuntime());
    }

    /** @test  */
    public function get_stored_value() {
        $this->assertEquals(null, $this->value->getStored());
    }

    /** @test  */
    public function set_stored_value() {
        $this->value->setStored(1);
        $this->assertEquals(1, $this->value->getStored());
        $this->value->setStored('test');
        $this->assertEquals('test', $this->value->getStored());
    }

    /** @test  */
    public function is_stored_value() {
        $this->assertFalse($this->value->isStored());
        $this->value->setStored(1);
        $this->assertTrue($this->value->isStored());
    }

    /** @test  */
    public function clear_stored_value() {
        $this->value->setStored(1);
        $this->value->clearStored();
        $this->assertFalse($this->value->isRuntime());
    }

    /** @test  */
    public function get_value() {
        $this->assertEquals(null, $this->value->getValue());
    }

    /** @test  */
    public function set_value() {
        $this->value->setValue(1);
        $this->assertEquals(1, $this->value->getValue());
        $this->assertEquals(1, $this->value->getRuntime());
        $this->assertEquals(null, $this->value->getStored());

        $this->value->clearValue();
        $this->assertEquals(null, $this->value->getValue());
        $this->assertEquals(null, $this->value->getRuntime());
        $this->assertEquals(null, $this->value->getStored());

        $this->value->setStored(1);
        $this->assertEquals(1, $this->value->getValue());
        $this->assertEquals(null, $this->value->getRuntime());
        $this->assertEquals(1, $this->value->getStored());

        $this->value->setValue(2);
        $this->assertEquals(2, $this->value->getValue());
        $this->assertEquals(2, $this->value->getRuntime());
        $this->assertEquals(1, $this->value->getStored());

        $this->value->clearValue();
        $this->assertEquals(1, $this->value->getValue());
        $this->assertEquals(null, $this->value->getRuntime());
        $this->assertEquals(1, $this->value->getStored());

        $this->value->setValue(1);
        $this->value->clearStored();
        $this->assertEquals(1, $this->value->getValue());
        $this->assertEquals(1, $this->value->getRuntime());
        $this->assertEquals(null, $this->value->getStored());
    }

    /** @test  */
    public function is_clean() {
        $this->assertTrue($this->value->isClean());
        $this->value->setValue(22);
        $this->assertFalse($this->value->isClean());
        $this->value->clearValue();
        $this->assertTrue($this->value->isClean());
        $this->value->setStored("220.02");
        $this->assertTrue($this->value->isClean());
        $this->value->setValue(220.03);
        $this->assertFalse($this->value->isClean());
    }
}