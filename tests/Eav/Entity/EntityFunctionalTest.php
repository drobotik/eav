<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Entity;

use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EntityBag;
use Kuperwood\Eav\EntityGnome;
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
     * @covers \Kuperwood\Eav\Entity::hasKey
     * @covers \Kuperwood\Eav\Entity::getKey
     * @covers \Kuperwood\Eav\Entity::setKey
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
     * @covers \Kuperwood\Eav\Entity::hasKey
     * @covers \Kuperwood\Eav\Entity::getKey
     * @covers \Kuperwood\Eav\Entity::setKey
     */
    public function key_zero() {
        $this->entity->setKey(0);
        $this->assertFalse($this->entity->hasKey());
        $this->assertEquals(0, $this->entity->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Entity::hasDomainKey
     * @covers \Kuperwood\Eav\Entity::getDomainKey
     * @covers \Kuperwood\Eav\Entity::setDomainKey
     */
    public function domain_key() {
        $this->entity->setDomainKey(0);
        $this->assertFalse($this->entity->hasDomainKey());
        $this->assertEquals(0, $this->entity->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Entity::hasDomainKey
     * @covers \Kuperwood\Eav\Entity::getDomainKey
     * @covers \Kuperwood\Eav\Entity::setDomainKey
     */
    public function domain_key_zero() {
        $this->entity->setDomainKey(0);
        $this->assertFalse($this->entity->hasDomainKey());
        $this->assertEquals(0, $this->entity->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Entity::setAttributeSet
     * @covers \Kuperwood\Eav\Entity::getAttributeSet
     * @covers \Kuperwood\Eav\Entity::__construct
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
     * @covers \Kuperwood\Eav\Entity::getBag
     * @covers \Kuperwood\Eav\Entity::__construct
     */
    public function bag() {
        $this->assertInstanceOf(EntityBag::class, $this->entity->getBag());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Entity::getGnome
     * @covers \Kuperwood\Eav\Entity::__construct
     */
    public function gnome() {
        $gnome = $this->entity->getGnome();
        $this->assertInstanceOf(EntityGnome::class, $this->entity->getGnome());
        $this->assertSame($this->entity, $gnome->getEntity());
    }
}