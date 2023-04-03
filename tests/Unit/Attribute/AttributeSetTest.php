<?php

namespace Tests\Unit\Attribute;

use Illuminate\Database\Eloquent\Collection;
use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
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
    public function get_containers() {
        $this->assertEquals([], $this->instance->getContainers());
    }

    /** @test */
    public function push_container() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->pushContainer($container);
        $this->assertEquals([$attribute->getName() => $container], $this->instance->getContainers());
    }

    /** @test */
    public function get_container() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->pushContainer($container);
        $this->assertSame($container, $this->instance->getContainer($attribute->getName()));
    }

    /** @test */
    public function get_container_return_nul() {
        $this->assertNull($this->instance->getContainer('test'));
    }

    /** @test */
    public function has_container() {
        $this->assertFalse($this->instance->hasContainer('test'));
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->pushContainer($container);
        $this->assertTrue($this->instance->hasContainer('test'));
    }

    /** @test */
    public function reset_containers() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->pushContainer($container);
        $this->instance->resetContainers();
        $this->assertEquals([], $this->instance->getContainers());
    }

    /** @test */
    public function fetch() {
        $key = 321;
        $collection = new Collection();
        $attribute = new AttributeModel();
        $collection->push($attribute);
        $attrSetModel = $this->getMockBuilder(AttributeSetModel::class)
            ->onlyMethods(['getAttrs'])
            ->getMock();
        $attrSetModel->expects($this->once())
            ->method('getAttrs')
            ->with($key)
            ->willReturn($collection);
        $instance = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['makeAttributeContainer', 'makeAttributeSetModel', 'pushContainer', 'getKey'])
            ->getMock();
        $instance->expects($this->once())
            ->method('getKey')
            ->willReturn($key);
        $instance->expects($this->once())
            ->method('makeAttributeSetModel')
            ->willReturn($attrSetModel);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['setAttributeSet', 'initialize'])
            ->getMock();
        $instance->expects($this->once())
            ->method('makeAttributeContainer')
            ->willReturn($container);
        $container->expects($this->once())
            ->method('setAttributeSet')
            ->with($instance);
        $container->expects($this->once())
            ->method('initialize')
            ->with($attribute);
        $instance->expects($this->once())
            ->method('pushContainer')
            ->with($container);
        $result = $instance->fetchContainers();
        $this->assertEquals($instance, $result);
    }
}