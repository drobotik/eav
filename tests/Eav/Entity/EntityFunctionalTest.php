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
    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new Entity();
    }
    /**
     * @test
     * @group functional
     * @covers Entity::hasKey, Entity::getKey, Entity::setKey
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
     * @covers Entity::hasKey, Entity::getKey, Entity::setKey
     */
    public function key_zero() {
        $this->entity->setKey(0);
        $this->assertFalse($this->entity->hasKey());
        $this->assertEquals(0, $this->entity->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers Entity::hasDomainKey, Entity::getDomainKey, Entity::setDomainKey
     */
    public function domain_key() {
        $this->entity->setDomainKey(0);
        $this->assertFalse($this->entity->hasDomainKey());
        $this->assertEquals(0, $this->entity->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers Entity::hasDomainKey, Entity::getDomainKey, Entity::setDomainKey
     */
    public function domain_key_zero() {
        $this->entity->setDomainKey(0);
        $this->assertFalse($this->entity->hasDomainKey());
        $this->assertEquals(0, $this->entity->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers Entity::setAttributeSet, Entity::getAttributeSet
     */
    public function attribute_set() {
        $this->assertInstanceOf(AttributeSet::class, $this->entity->getAttributeSet());
        $attributeSet = new AttributeSet();
        $this->entity->setAttributeSet($attributeSet);
        $this->assertSame($attributeSet, $this->entity->getAttributeSet());
        $this->assertSame($this->entity, $attributeSet->getEntity());
    }
    /**
     * @test
     * @group functional
     * @covers Entity::getBag
     */
    public function bag() {
        $this->assertInstanceOf(EntityBag::class, $this->entity->getBag());
    }
    /**
     * @test
     * @group functional
     * @covers Entity::getGnome
     */
    public function gnome() {
        $gnome = $this->entity->getGnome();
        $this->assertInstanceOf(EntityGnome::class, $this->entity->getGnome());
        $this->assertSame($this->entity, $gnome->getEntity());
    }
}