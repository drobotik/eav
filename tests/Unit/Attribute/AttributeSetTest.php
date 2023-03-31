<?php

namespace Tests\Unit\Attribute;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Exception\AttributeSetException;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
use Tests\TestCase;
use Throwable;

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
        $this->instance->push($container);
        $this->assertEquals([$attribute->getName() => $container], $this->instance->getContainers());
    }

    /** @test */
    public function get_container() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->push($container);
        $this->assertSame($container, $this->instance->getContainer($attribute->getName()));
    }

    /** @test */
    public function has_container() {
        $this->assertFalse($this->instance->hasContainer('test'));
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->push($container);
        $this->assertTrue($this->instance->hasContainer('test'));
    }

    /** @test */
    public function get_container_throws_exception() {
        $this->expectException(AttributeSetException::class);
        $this->expectExceptionMessage(sprintf(AttributeSetException::UNDEFINED_ATTRIBUTE, 'test'));
        $this->instance->getContainer('test');
    }

    /** @test */
    public function reset_containers() {
        $attribute = new Attribute();
        $attribute->setName('test');
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->instance->push($container);
        $this->instance->reset();
        $this->assertEquals([], $this->instance->getContainers());
    }

    /** @test */
    public function get_record() {
        $record = $this->eavFactory->createAttributeSet();
        $this->instance->setKey($record->getKey());
        $result = $this->instance->getRecord();
        $this->assertInstanceOf(AttributeSetModel::class, $result);
        $this->assertEquals($record->toArray(), $result->toArray());
    }

    public function get_record_throws_exception() {
        $this->expectException(Throwable::class);
        $this->instance->getRecord();
    }

    /** @test */
    public function get_record_attributes() {
        $collection = new Collection(123);
        $belongsToMany = $this->getMockBuilder(BelongsToMany::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();
        $belongsToMany->expects($this->once())
            ->method('get')
            ->willReturn($collection);
        $record = $this->getMockBuilder(AttributeSetModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['attributes'])
            ->getMock();
        $record->expects($this->once())
            ->method('attributes')
            ->willReturn($belongsToMany);
        $instance = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getRecord'])
            ->getMock();
        $instance->expects($this->once())
            ->method('getRecord')
            ->willReturn($record);
        $result = $instance->getRecordAttributes();
        $this->assertSame($collection, $result);
    }

    /** @test */
    public function fetch() {
        $collection = new Collection();
        $attribute = new AttributeModel();
        $collection->push($attribute);
        $instance = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['makeAttributeContainer', 'getRecordAttributes', 'push'])
            ->getMock();
        $instance->expects($this->once())
            ->method('getRecordAttributes')
            ->willReturn($collection);
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
            ->method('push')
            ->with($container);

        $result = $instance->fetch();
        $this->assertEquals($instance, $result);
    }
}