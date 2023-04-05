<?php

namespace Tests\Unit\Entity;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EntityAction;
use Kuperwood\Eav\EntityBag;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Exception\EntityException;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\ValueStringModel;
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
    public function find_no_key() {
        $result = $this->entity->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function find_no_record() {
        $this->entity->setKey(123);
        $result = $this->entity->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::NOT_FOUND->message(), $result->getMessage());
    }

    /** @test */
    public function find() {
        $domainModel = $this->eavFactory->createDomain();
        $setModel = $this->eavFactory->createAttributeSet();
        $entityModel = $this->eavFactory->createEntity($domainModel, $setModel);

        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'setKey', 'setEntity'])
            ->getMock();
        $set->expects($this->once())->method('setKey')->with($setModel->getKey());
        $set->expects($this->once())->method('fetchContainers');
        $set->expects($this->once())->method('setEntity');
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['makeAttributeSet', 'setAttributeSet', 'setDomainKey'])
            ->getMock();
        $entity->expects($this->once())
            ->method('setDomainKey')
            ->with($domainModel->getKey());
        $entity->expects($this->once())
            ->method('makeAttributeSet')
            ->willReturn($set);
        $entity->expects($this->once())
            ->method('setAttributeSet')
            ->with($set);

        $entity->setKey($entityModel->getKey());

        $result = $entity->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $result->getMessage());
    }

    /** @test */
    public function save_no_entity_key_no_domain() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_DOMAIN_KEY);
        $this->entity->save();
    }

    /** @test */
    public function save_no_entity_key_no_attr_set_key() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::UNDEFINED_ATTRIBUTE_SET_KEY);
        $this->entity->setDomainKey(1);
        $this->entity->save();
    }

    /** @test */
    public function save_no_entity_key_domain_not_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::DOMAIN_NOT_FOUND);
        $this->entity->setDomainKey(1);
        $this->entity->getAttributeSet()->setKey(1);
        $this->entity->save();
    }

    /** @test */
    public function save_no_entity_key_attr_set_not_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        $domain = $this->eavFactory->createDomain();
        $this->entity->setDomainKey($domain->getKey());
        $this->entity->getAttributeSet()->setKey(1);
        $this->entity->save();
    }

    /** @test */
    public function save_no_entity_key_creating_record() {
        $this->eavFactory->createDomain();
        $this->eavFactory->createAttributeSet();
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain);
        $this->entity->setDomainKey($domain->getKey());
        $this->entity->getAttributeSet()->setKey($attrSet->getKey());

        $this->entity->save();

        $this->assertEquals(1, EntityModel::count());
        $record = EntityModel::where(_ENTITY::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ENTITY::ATTR_SET_ID->column(), $attrSet->getKey())
            ->first();
        $this->assertInstanceOf(EntityModel::class, $record);
        $this->assertEquals($record->getKey(), $this->entity->getKey());
        $this->assertEquals($record->getDomainKey(), $this->entity->getDomainKey());
        $this->assertEquals($record->getAttrSetKey(), $this->entity->getAttributeSet()->getKey());
    }

    /** @test */
    public function save_with_entity_key_no_record_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ENTITY_NOT_FOUND);
        $this->entity->setKey(123);
        $this->entity->save();
    }

    /** @test */
    public function save_with_entity_key_no_domain_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::DOMAIN_NOT_FOUND);
        $entity = new EntityModel();
        $entity ->setDomainKey(123);
        $entity ->setAttrSetKey(123);
        $entity ->save();
        $this->entity->setKey($entity->getKey());
        $this->entity->save();
    }

    /** @test */
    public function save_with_entity_key_no_attr_set_in_db() {
        $this->expectException(EntityException::class);
        $this->expectExceptionMessage(EntityException::ATTR_SET_NOT_FOUND);
        $domain = $this->eavFactory->createDomain();
        $entity = new EntityModel();
        $entity ->setDomainKey($domain->getKey());
        $entity ->setAttrSetKey(123);
        $entity ->save();
        $this->entity->setKey($entity->getKey());
        $this->entity->save();
    }

    /** @test */
    public function save_with_entity_key_fetching_record() {
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain);
        $entity = $this->eavFactory->createEntity($domain, $attrSet);
        $this->entity->setKey($entity->getKey());

        $this->entity->save();

        $this->assertEquals(1, EntityModel::count());
        $record = EntityModel::where(_ENTITY::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ENTITY::ATTR_SET_ID->column(), $attrSet->getKey())
            ->first();
        $this->assertInstanceOf(EntityModel::class, $record);
        $this->assertEquals($record->getKey(), $this->entity->getKey());
        $this->assertEquals($record->getDomainKey(), $this->entity->getDomainKey());
        $this->assertEquals($record->getAttrSetKey(), $this->entity->getAttributeSet()->getKey());
    }

    /** @test */
    public function save_fetch_containers() {
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers'])
            ->getMock();
        $set->expects($this->once())->method('fetchContainers');
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet','beforeSave'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);
        $entity->save();
    }

    /** @test */
    public function save_values() {
        $data = [
            "phone" => "1234567890",
            "email" => "test@email.com"
        ];
        $bag = $this->getMockBuilder(EntityBag::class)
            ->onlyMethods(['getData', 'clear'])
            ->getMock();
        $bag->expects($this->once())
            ->method('getData')
            ->willReturn($data);
        $bag->expects($this->once())
            ->method('clear');
        $entityAction = $this->getMockBuilder(EntityAction::class)
            ->onlyMethods(['saveValue'])
            ->getMock();
        $entityAction->expects($this->exactly(2))
            ->method('saveValue')
            ->with($this->callback(fn($arg) => in_array($arg, array_values($data))));
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getEntityAction'])
            ->getMock();
        $container->expects($this->exactly(2))
            ->method('getEntityAction')
            ->willReturn($entityAction);
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'getContainer'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('fetchContainers');
        $attrSet->expects($this->exactly(2))
            ->method('getContainer')
            ->with($this->callback(fn($arg) => key_exists($arg, $data)))
            ->willReturn($container);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet', 'getBag', 'beforeSave'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $entity->expects($this->once())
            ->method('getBag')
            ->willReturn($bag);
        $entity->save();
    }

    /** @test */
    public function save_result_created() {
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('fetchContainers');
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet','beforeSave'])
            ->getMock();
        $entity->expects($this->once())
            ->method('beforeSave')
            ->willReturn(1);
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $result = $entity->save();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }

    /** @test */
    public function save_result_updated() {
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('fetchContainers');
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet','beforeSave'])
            ->getMock();
        $entity->expects($this->once())
            ->method('beforeSave')
            ->willReturn(2);
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $result = $entity->save();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }

    /** @test */
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

        $this->entity->setDomainKey($domain->getKey());
        $this->entity->getAttributeSet()->setKey($attrSet->getKey());
        $this->entity->getBag()->setFields($testData);

        $result = $this->entity->save();

        $emailRecord = ValueStringModel::where(_VALUE::ATTRIBUTE_ID->column(), $attrEmail->getKey())
            ->firstOrFail();
        $this->assertEquals($testData["email"], $emailRecord->getValue());
        $phoneRecord = ValueStringModel::where(_VALUE::ATTRIBUTE_ID->column(), $attrPhone->getKey())
            ->firstOrFail();
        $this->assertEquals($testData["phone"], $phoneRecord->getValue());

        $this->assertNull(ValueStringModel::where(_VALUE::ATTRIBUTE_ID->column(), $attrNote->getKey())->first());

        $this->assertEquals([], $this->entity->getBag()->getData());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());

        $resultData = $result->getData();
        $this->assertCount(2, $resultData);

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
    }

    /** @test */
    public function validate_with_errors() {
        $attribute = $this->getMockBuilder(Attribute::class)
            ->onlyMethods(['getName'])
            ->getMock();
        $attribute->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('email', 'phone');
        $action = $this->getMockBuilder(EntityAction::class)
            ->onlyMethods(['validateField'])
            ->getMock();
        $emailErrors = ['value' => 'invalid'];
        $phoneErrors = ['value' => 'not valid'];
        $action->expects($this->exactly(2))
            ->method('validateField')
            ->willReturn($emailErrors, $phoneErrors);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getEntityAction', 'getAttribute'])
            ->getMock();
        $container->expects($this->exactly(2))
            ->method('getEntityAction')
            ->willReturn($action);
        $container->expects($this->exactly(2))
            ->method('getAttribute')
            ->willReturn($attribute);
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'getContainers'])
            ->getMock();
        $set->expects($this->once())
            ->method('fetchContainers');
        $set->expects($this->once())
            ->method('getContainers')
            ->willReturn([$container, $container]);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);

        $result = $entity->validate();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_FAILS->code(), $result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_FAILS->message(), $result->getMessage());
        $this->assertEquals(['email' => $emailErrors, 'phone' => $phoneErrors], $result->getData());
    }

    /** @test */
    public function validate_passed() {
        $action = $this->getMockBuilder(EntityAction::class)
            ->onlyMethods(['validateField'])
            ->getMock();
        $emailErrors = null;
        $phoneErrors = null;
        $action->expects($this->exactly(2))
            ->method('validateField')
            ->willReturn($emailErrors, $phoneErrors);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getEntityAction', 'getAttribute'])
            ->getMock();
        $container->expects($this->exactly(2))
            ->method('getEntityAction')
            ->willReturn($action);
        $container->expects($this->never())
            ->method('getAttribute');
        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['fetchContainers', 'getContainers'])
            ->getMock();
        $set->expects($this->once())
            ->method('fetchContainers');
        $set->expects($this->once())
            ->method('getContainers')
            ->willReturn([$container, $container]);
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($set);

        $result = $entity->validate();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_PASSED->code(), $result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_PASSED->message(), $result->getMessage());
        $this->assertNull($result->getData());
    }

}