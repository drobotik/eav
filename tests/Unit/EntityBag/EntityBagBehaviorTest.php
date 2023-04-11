<?php

declare(strict_types=1);

namespace Tests\Unit\EntityBag;

use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Entity;
use Drobotik\Eav\EntityBag;
use Drobotik\Eav\Value\ValueManager;
use PHPUnit\Framework\TestCase;

class EntityBagBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers EntityBag::setField
     */
    public function set_field() {
        $attrName = 'email';
        $attrValue = 'email@email.net';
        $valueManager = $this->getMockBuilder(ValueManager::class)
            ->onlyMethods(['setValue'])
            ->getMock();
        $valueManager->expects($this->once())
            ->method('setValue')
            ->with($attrValue);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getValueManager'])
            ->getMock();
        $container->expects($this->once())
            ->method('getValueManager')
            ->willReturn($valueManager);
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getContainer', 'hasContainer'])
            ->getMock();
        $set->expects($this->once())
            ->method('hasContainer')
            ->with($attrName)
            ->willReturn(true);
        $set->expects($this->once())
            ->method('getContainer')
            ->with($attrName)
            ->willReturn($container);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);
        $bag = $this->getMockBuilder(EntityBag::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $bag->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $bag->setField($attrName, $attrValue);
    }
    /**
     * @test
     * @group behavior
     * @covers EntityBag::setField
     */
    public function set_field_result() {

        $valueManager = new ValueManager;
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getValueManager'])
            ->getMock();
        $container->method('getValueManager')->willReturn($valueManager);
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getContainer', 'hasContainer'])
            ->getMock();
        $set->method('hasContainer')->willReturn(true);
        $set->method('getContainer')->willReturn($container);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->method('getAttributeSet')->willReturn($set);
        $bag = $this->getMockBuilder(EntityBag::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $bag->method('getEntity')->willReturn($entity);
        $bag->setField('email', 'test');

        $this->assertEquals('test', $valueManager->getRuntime());

    }
    /**
     * @test
     * @group behavior
     * @covers EntityBag::removeField
     */
    public function remove_field() {
        $attrName = 'email';
        $valueManager = $this->getMockBuilder(ValueManager::class)
            ->onlyMethods(['clearRuntime'])
            ->getMock();
        $valueManager->expects($this->once())
            ->method('clearRuntime');
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getValueManager'])
            ->getMock();
        $container->expects($this->once())
            ->method('getValueManager')
            ->willReturn($valueManager);
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getContainer', 'hasContainer'])
            ->getMock();
        $set->expects($this->once())
            ->method('hasContainer')
            ->with($attrName)
            ->willReturn(true);
        $set->expects($this->once())
            ->method('getContainer')
            ->with($attrName)
            ->willReturn($container);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);
        $bag = $this->getMockBuilder(EntityBag::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $bag->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $bag->removeField($attrName);
    }
    /**
     * @test
     * @group behavior
     * @covers EntityBag::setFields
     */
    public function set_fields() {
        $data = ['one' => 1, 'two' => 2, 'three' => 3];
        $bag = $this->getMockBuilder(EntityBag::class)
            ->onlyMethods(['setField'])
            ->getMock();
        $bag->expects($this->exactly(3))
            ->method('setField')
            ->with($this->logicalOr('one', 'two', 'three'));
        $bag->setFields($data);
    }
}