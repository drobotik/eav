<?php

namespace Tests\Unit\Entity;

use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EntityGnome;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Transporter;
use Tests\TestCase;

class EntityTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new Entity();
    }

    /** @test */
    public function key() {
        $this->assertFalse($this->entity->hasKey());
        $this->entity->setKey(1);
        $this->assertEquals(1, $this->entity->getKey());
        $this->assertTrue($this->entity->hasKey());
    }

    /** @test */
    public function domain_key() {
        $this->assertFalse($this->entity->hasDomainKey());
        $this->entity->setDomainKey(1);
        $this->assertEquals(1, $this->entity->getDomainKey());
        $this->assertTrue($this->entity->hasDomainKey());
    }

    /** @test */
    public function attribute_set() {
        $this->assertInstanceOf(AttributeSet::class, $this->entity->getAttributeSet());
        $attributeSet = new AttributeSet();
        $this->entity->setAttributeSet($attributeSet);
        $this->assertSame($attributeSet, $this->entity->getAttributeSet());
        $this->assertSame($this->entity, $attributeSet->getEntity());
    }

    /** @test */
    public function bag() {
        $this->assertInstanceOf(Transporter::class, $this->entity->getBag());
    }

    /** @test */
    public function gnome() {
        $gnome = $this->entity->getGnome();
        $this->assertInstanceOf(EntityGnome::class, $this->entity->getGnome());
        $this->assertSame($this->entity, $gnome->getEntity());
    }

    /** @test */
    public function find() {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getGnome'])
            ->getMock();
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['find'])
            ->getMock();
        $result = (new Result())->found();
        $gnome->expects($this->once())
            ->method('find')
            ->willReturn($result);
        $entity->expects($this->once())
            ->method('getGnome')
            ->willReturn($gnome);
        $this->assertSame($result, $entity->find());
    }

    /** @test */
    public function save() {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getGnome'])
            ->getMock();
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['save'])
            ->getMock();
        $result = (new Result())->created();
        $gnome->expects($this->once())
            ->method('save')
            ->willReturn($result);
        $entity->expects($this->once())
            ->method('getGnome')
            ->willReturn($gnome);
        $this->assertSame($result, $entity->save());
    }

    /** @test */
    public function delete() {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getGnome'])
            ->getMock();
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['delete'])
            ->getMock();
        $result = (new Result())->deleted();
        $gnome->expects($this->once())
            ->method('delete')
            ->willReturn($result);
        $entity->expects($this->once())
            ->method('getGnome')
            ->willReturn($gnome);
        $this->assertSame($result, $entity->delete());
    }

    /** @test */
    public function validate() {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getGnome'])
            ->getMock();
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['validate'])
            ->getMock();
        $result = (new Result())->deleted();
        $gnome->expects($this->once())
            ->method('validate')
            ->willReturn($result);
        $entity->expects($this->once())
            ->method('getGnome')
            ->willReturn($gnome);
        $this->assertSame($result, $entity->validate());
    }

    /** @test */
    public function to_array() {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getGnome'])
            ->getMock();
        $gnome = $this->getMockBuilder(EntityGnome::class)
            ->setConstructorArgs([$entity])
            ->onlyMethods(['toArray'])
            ->getMock();
        $result = [123];
        $gnome->expects($this->once())
            ->method('toArray')
            ->willReturn($result);
        $entity->expects($this->once())
            ->method('getGnome')
            ->willReturn($gnome);
        $this->assertEquals($result, $entity->toArray());
    }

}