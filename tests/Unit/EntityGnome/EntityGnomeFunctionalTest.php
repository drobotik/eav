<?php

declare(strict_types=1);

namespace Tests\Unit\EntityGnome;

use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EntityGnome;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Exception\EntityException;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Result\Result;
use Tests\TestCase;

class EntityGnomeFunctionalTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $entity = new Entity();
        $this->gnome = new EntityGnome($entity);
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::find
     */
    public function find_no_key() {
        $result = $this->gnome->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::find
     */
    public function find_no_record() {
        $this->gnome->getEntity()->setKey(123);
        $result = $this->gnome->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::NOT_FOUND->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_no_entity_key_no_domain() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_DOMAIN_KEY);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_no_entity_key_no_attr_set_key() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_ATTRIBUTE_SET_KEY);
        $this->gnome->getEntity()->setDomainKey(1);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_no_entity_key_domain_not_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::DOMAIN_NOT_FOUND);
        $entity = $this->gnome->getEntity();
        $entity->setDomainKey(1);
        $entity->getAttributeSet()->setKey(1);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_no_entity_key_attr_set_not_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        $domain = $this->eavFactory->createDomain();
        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domain->getKey());
        $entity->getAttributeSet()->setKey(1);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_no_entity_key_creating_record() {
        $this->eavFactory->createDomain();
        $this->eavFactory->createAttributeSet();
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain);
        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domain->getKey());
        $entity->getAttributeSet()->setKey($attrSet->getKey());

        $this->gnome->save();

        $this->assertEquals(1, EntityModel::count());
        $record = EntityModel::where(_ENTITY::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ENTITY::ATTR_SET_ID->column(), $attrSet->getKey())
            ->first();
        $this->assertInstanceOf(EntityModel::class, $record);
        $this->assertEquals($record->getKey(), $entity->getKey());
        $this->assertEquals($record->getDomainKey(), $entity->getDomainKey());
        $this->assertEquals($record->getAttrSetKey(), $entity->getAttributeSet()->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_with_entity_key_no_record_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ENTITY_NOT_FOUND);
        $this->gnome->getEntity()->setKey(123);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_with_entity_key_no_domain_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::DOMAIN_NOT_FOUND);
        $entity = new EntityModel();
        $entity ->setDomainKey(123);
        $entity ->setAttrSetKey(123);
        $entity ->save();
        $this->gnome->getEntity()->setKey($entity->getKey());
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_with_entity_key_no_attr_set_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        $domain = $this->eavFactory->createDomain();
        $entity = new EntityModel();
        $entity->setDomainKey($domain->getKey());
        $entity->setAttrSetKey(123);
        $entity->save();
        $this->gnome->getEntity()->setKey($entity->getKey());
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_with_entity_key_fetching_record() {
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain);
        $entityModel = $this->eavFactory->createEntity($domain, $attrSet);
        $entity = $this->gnome->getEntity();
        $entity->setKey($entityModel->getKey());

        $this->gnome->save();

        $this->assertEquals(1, EntityModel::count());
        $record = EntityModel::where(_ENTITY::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ENTITY::ATTR_SET_ID->column(), $attrSet->getKey())
            ->first();
        $this->assertInstanceOf(EntityModel::class, $record);
        $this->assertEquals($record->getKey(), $entity->getKey());
        $this->assertEquals($record->getDomainKey(), $entity->getDomainKey());
        $this->assertEquals($record->getAttrSetKey(), $entity->getAttributeSet()->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers EntityGnome::save
     */
    public function save_results() {
        $testData = [
            "email" => "test@email.net",
            'phone' => '1234567890',
        ];

        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain);
        $group = $this->eavFactory->createGroup($attrSet);
        $attrEmail = $this->eavFactory->createAttribute($domain, [
            _ATTR::NAME->column() => "email"
        ]);
        $attrPhone = $this->eavFactory->createAttribute($domain, [
            _ATTR::NAME->column() => "phone"
        ]);
        $attrNote = $this->eavFactory->createAttribute($domain, [
            _ATTR::NAME->column() => "note"
        ]);
        $this->eavFactory->createPivot($domain, $attrSet, $group, $attrEmail);
        $this->eavFactory->createPivot($domain, $attrSet, $group, $attrPhone);
        $this->eavFactory->createPivot($domain, $attrSet, $group, $attrNote);

        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domain->getKey());
        $set = $entity->getAttributeSet();
        $set->setKey($attrSet->getKey());
        $bag = $entity->getBag();
        $bag->setFields($testData);

        $result = $this->gnome->save();

        $emailRecord = ValueStringModel::where(_VALUE::ATTRIBUTE_ID->column(), $attrEmail->getKey())
            ->firstOrFail();

        $this->assertEquals($testData["email"], $emailRecord->getValue());
        $phoneRecord = ValueStringModel::where(_VALUE::ATTRIBUTE_ID->column(), $attrPhone->getKey())
            ->firstOrFail();
        $this->assertEquals($testData["phone"], $phoneRecord->getValue());

        $this->assertNull(ValueStringModel::where(_VALUE::ATTRIBUTE_ID->column(), $attrNote->getKey())->first());

        $this->assertEquals([], $entity->getBag()->getData());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());

        $resultData = $result->getData();
        $this->assertCount(3, $resultData);

        $this->assertArrayHasKey('email', $resultData);
        $data = $resultData['email'];
        $this->assertInstanceOf(Result::class, $data);
        $this->assertEquals(_RESULT::CREATED->code(), $data->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $data->getMessage());

        $this->assertArrayHasKey('phone', $resultData);
        $data = $resultData['phone'];
        $this->assertInstanceOf(Result::class, $data);
        $this->assertEquals(_RESULT::CREATED->code(), $data->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $data->getMessage());

        $this->assertArrayHasKey('note', $resultData);
        $data = $resultData['note'];
        $this->assertInstanceOf(Result::class, $data);
        $this->assertEquals(_RESULT::EMPTY->code(), $data->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $data->getMessage());
    }
}