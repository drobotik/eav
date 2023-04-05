<?php

namespace Tests\Unit\Entity;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EntityBag;
use Kuperwood\Eav\ValueManager;
use Tests\TestCase;

class EntityBagTest extends TestCase
{
    /** @test */
    public function entity() {
        $entity = new Entity();
        $bag = $entity->getBag();
        $this->assertSame($entity, $bag->getEntity());
        $entity2 = new Entity();
        $bag->setEntity($entity2);
        $this->assertSame($entity2, $bag->getEntity());
    }

    /** @test */
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

    /** @test */
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
    /** @test */
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