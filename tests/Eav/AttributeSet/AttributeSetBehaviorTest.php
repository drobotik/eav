<?php

declare(strict_types=1);

namespace Tests\Eav\AttributeSet;

use Illuminate\Database\Eloquent\Collection;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\AttributeSetAction;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;
use PHPUnit\Framework\TestCase;

class AttributeSetBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers AttributeSet::fetchContainers
     */
    public function fetch_containers() {
        $key = 321;
        $collection = new Collection();
        $attribute = new AttributeModel();
        $collection->push($attribute);
        $attrSetModel = $this->getMockBuilder(AttributeSetModel::class)
            ->onlyMethods(['findAttributes'])
            ->getMock();
        $attrSetModel->expects($this->once())
            ->method('findAttributes')
            ->with($key)
            ->willReturn($collection);
        $instance = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['makeAttributeContainer', 'makeAttributeSetModel', 'pushContainer', 'getKey', 'hasKey'])
            ->getMock();
        $instance->expects($this->once())
            ->method('hasKey')
            ->willReturn(true);
        $instance->expects($this->once())
            ->method('getKey')
            ->willReturn($key);
        $instance->expects($this->once())
            ->method('makeAttributeSetModel')
            ->willReturn($attrSetModel);
        $attrSetAction = $this->getMockBuilder(AttributeSetAction::class)
            ->onlyMethods(['initialize'])
            ->getMock();
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['setAttributeSet', 'getAttributeSetAction'])
            ->getMock();
        $container->expects($this->once())
            ->method('getAttributeSetAction')
            ->willReturn($attrSetAction);
        $instance->expects($this->once())
            ->method('makeAttributeContainer')
            ->willReturn($container);
        $container->expects($this->once())
            ->method('setAttributeSet')
            ->with($instance);
        $instance->expects($this->once())
            ->method('pushContainer')
            ->with($container);
        $result = $instance->fetchContainers();
        $this->assertEquals($instance, $result);
    }

    /**
     * @test
     * @group behavior
     * @covers AttributeSet::fetchContainers
     */
    public function fetch_containers_no_key() {
        $instance = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['pushContainer'])
            ->getMock();
        $instance->expects($this->never())
            ->method('pushContainer');
        $result = $instance->fetchContainers();
        $this->assertEquals($instance, $result);
    }
}