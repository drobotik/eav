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
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_GROUP;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Tests\TestCase;

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

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Entity::findByKey
     */
    public function find_by_key()
    {
        $eavFactory = $this->eavFactory;
        $domainKey = $eavFactory->createDomain();
        $setKey = $eavFactory->createAttributeSet($domainKey);
        $group1Key = $eavFactory->createGroup($setKey, [_GROUP::NAME => 'group1']);
        $attr1Key = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "attr1",
            _ATTR::TYPE => ATTR_TYPE::STRING
        ]);
        $attr2Key = $eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "attr2",
            _ATTR::TYPE => ATTR_TYPE::STRING
        ]);

        $eavFactory->createPivot($domainKey, $setKey, $group1Key, $attr1Key);
        $eavFactory->createPivot($domainKey, $setKey, $group1Key, $attr2Key);
        $data = [
            "attr1" => 'value1',
            "attr2" => 'value2',
        ];
        $entity = new Entity();
        $entity->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey($setKey);
        $entity->getBag()->setFields($data);
        $entity->save();

        $entityKey = $entity->getKey();

        $entity = Entity::findByKey($entityKey);
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals($domainKey, $entity->getDomainKey());
        $this->assertEquals($setKey, $entity->getAttributeSet()->getKey());
        $this->assertEquals($data, $entity->toArray());

        $this->assertNull(Entity::findByKey(123));
    }
}