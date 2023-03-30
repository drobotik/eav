<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeSet;
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
    /** @test */
    public function fetch() {
        $domain = $this->eavFactory->createDomain();
        $set = $this->eavFactory->createAttributeSet($domain);
        $group = $this->eavFactory->createGroup($set);
        $attr1 = $this->eavFactory->createAttribute($domain);
        $attr2 = $this->eavFactory->createAttribute($domain);
        $attr3 = $this->eavFactory->createAttribute($domain);
        $this->eavFactory->createPivot($domain, $set, $group, $attr1);
        $this->eavFactory->createPivot($domain, $set, $group, $attr2);
        $this->eavFactory->createPivot($domain, $set, $group, $attr3);

        $this->instance->setKey($set->getKey());
        $this->instance->setName($set->getName());

        $result = $this->instance->fetch();
        $this->assertSame($this->instance, $result);

        $attributes = $this->instance->getAttributes();
        $this->assertCount(3, $attributes);
        $result = $this->instance->getAttribute($attr1->getName());
        $this->assertEquals($attr1->toArray(), $result->getBag()->getFields());
        $result = $this->instance->getAttribute($attr2->getName());
        $this->assertEquals($attr2->toArray(), $result->getBag()->getFields());
        $result = $this->instance->getAttribute($attr3->getName());
        $this->assertEquals($attr3->toArray(), $result->getBag()->getFields());
    }
}