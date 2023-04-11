<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use Drobotik\Eav\Entity;
use Drobotik\Eav\EntityGnome;
use Drobotik\Eav\Result\Result;
use PHPUnit\Framework\TestCase;

class EntityBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers Entity::find
     */
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
    /**
     * @test
     * @group behavior
     * @covers Entity::save
     */
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
    /**
     * @test
     * @group behavior
     * @covers Entity::delete
     */
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
    /**
     * @test
     * @group behavior
     * @covers Entity::validate
     */
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
    /**
     * @test
     * @group behavior
     * @covers Entity::toArray
     */
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