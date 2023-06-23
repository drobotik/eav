<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityGnome;

use Drobotik\Eav\Entity;
use Drobotik\Eav\EntityGnome;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Exception\EntityException;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Result\Result;
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
     * @covers \Drobotik\Eav\EntityGnome::find
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
     * @covers \Drobotik\Eav\EntityGnome::find
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
     * @covers \Drobotik\Eav\EntityGnome::save
     */
    public function save_no_entity_key_no_domain() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_DOMAIN_KEY);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
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
     * @covers \Drobotik\Eav\EntityGnome::save
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
     * @covers \Drobotik\Eav\EntityGnome::save
     */
    public function save_no_entity_key_attr_set_not_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        $domainKey = $this->eavFactory->createDomain();
        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey(1);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
     */
    public function save_no_entity_key_creating_record() {
        $this->eavFactory->createDomain();
        $this->eavFactory->createAttributeSet();
        $domainKey = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domainKey);
        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey($attrSet->getKey());

        $this->gnome->save();

        $model = new EntityModel();
        $model->setDomainKey($domainKey);
        $model->setSetKey($attrSet->getKey());
        $this->assertEquals(1, $model->count());
        $record = $model->getBySetAndDomain()[0];

        $this->assertIsArray($record);
        $this->assertEquals($record[_ENTITY::ID->column()], $entity->getKey());
        $this->assertEquals($record[_ENTITY::DOMAIN_ID->column()], $entity->getDomainKey());
        $this->assertEquals($record[_ENTITY::ATTR_SET_ID->column()], $entity->getAttributeSet()->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
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
     * @covers \Drobotik\Eav\EntityGnome::save
     */
    public function save_with_entity_key_no_domain_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::DOMAIN_NOT_FOUND);
        $entity = new EntityModel();
        $entity->setDomainKey(123);
        $entity->setSetKey(123);
        $entityKey = $entity->create();
        $this->gnome->getEntity()->setKey($entityKey);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
     */
    public function save_with_entity_key_no_attr_set_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        $domainKey = $this->eavFactory->createDomain();
        $entity = new EntityModel();
        $entity->setDomainKey($domainKey);
        $entity->setSetKey(123);
        $entityKey = $entity->create();
        $this->gnome->getEntity()->setKey($entityKey);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
     */
    public function save_with_entity_key_fetching_record() {
        $domainKey = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domainKey);
        $entityKey = $this->eavFactory->createEntity($domainKey, $attrSet->getKey());
        $entity = $this->gnome->getEntity();
        $entity->setKey($entityKey);

        $this->gnome->save();


        $model = new EntityModel();
        $model->setDomainKey($domainKey);
        $model->setSetKey($attrSet->getKey());
        $this->assertEquals(1, $model->count());
        $record = $model->getBySetAndDomain()[0];

        $this->assertIsArray($record);
        $this->assertEquals($record[_ENTITY::ID->column()], $entity->getKey());
        $this->assertEquals($record[_ENTITY::DOMAIN_ID->column()], $entity->getDomainKey());
        $this->assertEquals($record[_ENTITY::ATTR_SET_ID->column()], $entity->getAttributeSet()->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
     */
    public function save_results() {
        $testData = [
            "email" => "test@email.net",
            'phone' => '1234567890',
        ];

        $domainKey = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domainKey);
        $group = $this->eavFactory->createGroup($attrSet->getKey());
        $attrEmail = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME->column() => "email"
        ]);
        $attrPhone = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME->column() => "phone"
        ]);
        $attrNote = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME->column() => "note"
        ]);
        $this->eavFactory->createPivot($domainKey, $attrSet->getKey(), $group->getKey(), $attrEmail->getKey());
        $this->eavFactory->createPivot($domainKey, $attrSet->getKey(), $group->getKey(), $attrPhone->getKey());
        $this->eavFactory->createPivot($domainKey, $attrSet->getKey(), $group->getKey(), $attrNote->getKey());

        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domainKey);
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
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::delete
     */
    public function delete_exception_if_not_set_key_provided() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_ATTRIBUTE_SET_KEY);
        $entity = $this->gnome->getEntity();
        $entity->setKey(1);
        $this->gnome->delete();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::delete
     */
    public function delete_exception_if_not_entity_key_provided() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_ENTITY_KEY);
        $entity = $this->gnome->getEntity();
        $entity->getAttributeSet()->setKey(1);
        $this->gnome->delete();
    }
}