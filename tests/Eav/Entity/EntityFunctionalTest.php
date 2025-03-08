<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Entity;

use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Entity;
use Drobotik\Eav\EntityBag;
use Drobotik\Eav\EntityGnome;
use PHPUnit\Framework\TestCase;

class EntityFunctionalTest extends TestCase
{
    private Entity $entity;
    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new Entity();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Entity::hasKey
     * @covers \Drobotik\Eav\Entity::getKey
     * @covers \Drobotik\Eav\Entity::setKey
     */
    public function key() {
        $this->assertFalse($this->entity->hasKey());
        $this->entity->setKey(1);
        $this->assertEquals(1, $this->entity->getKey());
        $this->assertTrue($this->entity->hasKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Entity::hasKey
     * @covers \Drobotik\Eav\Entity::getKey
     * @covers \Drobotik\Eav\Entity::setKey
     */
    public function key_zero() {
        $this->entity->setKey(0);
        $this->assertFalse($this->entity->hasKey());
        $this->assertEquals(0, $this->entity->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Entity::hasDomainKey
     * @covers \Drobotik\Eav\Entity::getDomainKey
     * @covers \Drobotik\Eav\Entity::setDomainKey
     */
    public function domain_key() {
        $this->entity->setDomainKey(0);
        $this->assertFalse($this->entity->hasDomainKey());
        $this->assertEquals(0, $this->entity->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Entity::hasDomainKey
     * @covers \Drobotik\Eav\Entity::getDomainKey
     * @covers \Drobotik\Eav\Entity::setDomainKey
     */
    public function domain_key_zero() {
        $this->entity->setDomainKey(0);
        $this->assertFalse($this->entity->hasDomainKey());
        $this->assertEquals(0, $this->entity->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Entity::setAttributeSet
     * @covers \Drobotik\Eav\Entity::getAttributeSet
     * @covers \Drobotik\Eav\Entity::__construct
     */
    public function attribute_set() {
        $attributeSet = $this->entity->getAttributeSet();
        $this->assertInstanceOf(AttributeSet::class, $this->entity->getAttributeSet());
        $this->assertSame($this->entity, $attributeSet->getEntity());

        $attributeSet = new AttributeSet();
        $this->entity->setAttributeSet($attributeSet);
        $this->assertSame($attributeSet, $this->entity->getAttributeSet());
        $this->assertSame($this->entity, $attributeSet->getEntity());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Entity::getBag
     * @covers \Drobotik\Eav\Entity::__construct
     */
    public function bag() {
        $this->assertInstanceOf(EntityBag::class, $this->entity->getBag());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Entity::getGnome
     * @covers \Drobotik\Eav\Entity::__construct
     */
    public function gnome() {
        $gnome = $this->entity->getGnome();
        $this->assertInstanceOf(EntityGnome::class, $this->entity->getGnome());
        $this->assertSame($this->entity, $gnome->getEntity());
    }
}