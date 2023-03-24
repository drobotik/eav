<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Exception\AttributeSetException;
use Tests\TestCase;

class AttributeSetTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->instance = new AttributeSet();
    }

    /** @test */
    public function key() {
        $this->instance->setKey(1);
        $this->assertEquals(1, $this->instance->getKey());
    }

    /** @test */
    public function name() {
        $this->instance->setName('test');
        $this->assertEquals('test', $this->instance->getName());
    }

    /** @test */
    public function entity() {
        $entity = new Entity();
        $this->instance->setEntity($entity);
        $this->assertSame($entity, $this->instance->getEntity());
    }

    /** @test */
    public function get_attributes() {
        $this->assertEquals([], $this->instance->getAttributes());
    }

    /** @test */
    public function push() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $this->instance->push($attribute);
        $this->assertEquals([$attribute->getName() => $attribute], $this->instance->getAttributes());
    }

    /** @test */
    public function get_attribute() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $this->instance->push($attribute);
        $this->assertSame($attribute, $this->instance->getAttribute($attribute->getName()));
    }

    /** @test */
    public function has_attribute() {
        $this->assertFalse($this->instance->hasAttribute('test'));
        $attribute = new Attribute();
        $attribute->setName('test');
        $this->instance->push($attribute);
        $this->assertTrue($this->instance->hasAttribute('test'));
    }

    /** @test */
    public function get_attribute_throws_exception() {
        $this->expectException(AttributeSetException::class);
        $this->expectExceptionMessage(sprintf(AttributeSetException::UNDEFINED_ATTRIBUTE, 'test'));
        $this->instance->getAttribute('test');
    }

    /** @test */
    public function reset() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $this->instance->push($attribute);
        $this->instance->reset();
        $this->assertEquals([], $this->instance->getAttributes());
    }
}