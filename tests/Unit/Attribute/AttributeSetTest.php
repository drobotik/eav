<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Tests\TestCase;

class AttributeSetTest extends TestCase
{
    /** @test */
    public function key() {
        $set = new AttributeSet();
        $set->setKey(1);
        $this->assertEquals(1, $set->getKey());
    }

    /** @test */
    public function name() {
        $set = new AttributeSet();
        $set->setName('test');
        $this->assertEquals('test', $set->getName());
    }

    /** @test */
    public function entity() {
        $set = new AttributeSet();
        $entity = new Entity();
        $set->setEntity($entity);
        $this->assertEquals($entity, $set->getEntity());
    }
}