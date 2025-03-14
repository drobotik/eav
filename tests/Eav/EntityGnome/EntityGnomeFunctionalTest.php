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
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\EntityException;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Result\Result;
use Tests\TestCase;

class EntityGnomeFunctionalTest extends TestCase
{
    private EntityGnome $gnome;
    public function setUp(): void
    {
        parent::setUp();
        $entity = new Entity();
        $this->gnome = new EntityGnome($entity);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::getEntity
     * @covers \Drobotik\Eav\EntityGnome::__construct
     */
    public function entity()
    {
        $entity = new Entity();
        $gnome = new EntityGnome($entity);
        $this->assertSame($entity, $gnome->getEntity());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::find
     */
    public function find_no_key() {
        $result = $this->gnome->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EMPTY), $result->getMessage());
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
        $this->assertEquals(_RESULT::NOT_FOUND, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_FOUND), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
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
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
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
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
     * @covers \Drobotik\Eav\EntityGnome::checkDomainExists
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
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
     * @covers \Drobotik\Eav\EntityGnome::checkAttrSetExist
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
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
     */
    public function save_no_entity_key_creating_record() {
        $this->eavFactory->createDomain();
        $this->eavFactory->createAttributeSet();
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domainKey);
        $entity->getAttributeSet()->setKey($setKey);

        $this->gnome->save();

        $model = new EntityModel();
        $this->assertEquals(1, $model->count());
        $record = $model->getBySetAndDomain($domainKey, $setKey)[0];

        $this->assertIsArray($record);
        $this->assertEquals($record[_ENTITY::ID], $entity->getKey());
        $this->assertEquals($record[_ENTITY::DOMAIN_ID], $entity->getDomainKey());
        $this->assertEquals($record[_ENTITY::ATTR_SET_ID], $entity->getAttributeSet()->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
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
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
     */
    public function save_with_entity_key_no_domain_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::DOMAIN_NOT_FOUND);
        $entity = new EntityModel();
        $entityKey = $entity->create([
            _ENTITY::DOMAIN_ID => 123,
            _ENTITY::ATTR_SET_ID => 123
        ]);
        $this->gnome->getEntity()->setKey($entityKey);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
     */
    public function save_with_entity_key_no_attr_set_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        $domainKey = $this->eavFactory->createDomain();
        $entity = new EntityModel();
        $entityKey = $entity->create([
            _ENTITY::DOMAIN_ID => $domainKey,
            _ENTITY::ATTR_SET_ID => 123
        ]);
        $this->gnome->getEntity()->setKey($entityKey);
        $this->gnome->save();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
     */
    public function save_with_entity_key_fetching_record() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $entityKey = $this->eavFactory->createEntity($domainKey, $setKey);
        $entity = $this->gnome->getEntity();
        $entity->setKey($entityKey);

        $this->gnome->save();

        $model = new EntityModel();
        $this->assertEquals(1, $model->count());
        $record = $model->getBySetAndDomain($domainKey, $setKey)[0];

        $this->assertIsArray($record);
        $this->assertEquals($record[_ENTITY::ID], $entity->getKey());
        $this->assertEquals($record[_ENTITY::DOMAIN_ID], $entity->getDomainKey());
        $this->assertEquals($record[_ENTITY::ATTR_SET_ID], $entity->getAttributeSet()->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\EntityGnome::save
     * @covers \Drobotik\Eav\EntityGnome::beforeSave
     */
    public function save_results() {
        $testData = [
            "email" => "test@email.net",
            'phone' => '1234567890',
        ];
        $valueModel = $this->makeValueModel();
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $attrEmailKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "email"
        ]);
        $attrPhoneKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "phone"
        ]);
        $attrNoteKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME => "note"
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attrEmailKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attrPhoneKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attrNoteKey);

        $entity = $this->gnome->getEntity();
        $entity->setDomainKey($domainKey);
        $set = $entity->getAttributeSet();
        $set->setKey($setKey);
        $bag = $entity->getBag();
        $bag->setFields($testData);

        $result = $this->gnome->save();

        $emailRecord = $valueModel->find(
            ATTR_TYPE::STRING,
            $domainKey,
            $entity->getKey(),
            $attrEmailKey
        );
        $this->assertIsArray($emailRecord);
        $this->assertEquals($testData["email"], $emailRecord[_VALUE::VALUE]);

        $phoneRecord = $valueModel->find(
            ATTR_TYPE::STRING,
            $domainKey,
            $entity->getKey(),
            $attrPhoneKey
        );
        $this->assertIsArray($phoneRecord);
        $this->assertEquals($testData["phone"], $phoneRecord[_VALUE::VALUE]);

        $noteRecord = $valueModel->find(
            ATTR_TYPE::STRING,
            $domainKey,
            $entity->getKey(),
            $attrNoteKey
        );
        $this->assertFalse($noteRecord);

        $this->assertEquals([], $entity->getBag()->getData());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $result->getMessage());

        $resultData = $result->getData();
        $this->assertCount(3, $resultData);

        $this->assertArrayHasKey('email', $resultData);
        $data = $resultData['email'];
        $this->assertInstanceOf(Result::class, $data);
        $this->assertEquals(_RESULT::CREATED, $data->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $data->getMessage());

        $this->assertArrayHasKey('phone', $resultData);
        $data = $resultData['phone'];
        $this->assertInstanceOf(Result::class, $data);
        $this->assertEquals(_RESULT::CREATED, $data->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $data->getMessage());

        $this->assertArrayHasKey('note', $resultData);
        $data = $resultData['note'];
        $this->assertInstanceOf(Result::class, $data);
        $this->assertEquals(_RESULT::EMPTY, $data->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EMPTY), $data->getMessage());
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