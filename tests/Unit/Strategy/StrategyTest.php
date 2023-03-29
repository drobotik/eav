<?php

namespace Tests\Unit\Strategy;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\ValueManager;
use Tests\Fixtures\StrategyFixture;
use Tests\TestCase;

class StrategyTest extends TestCase
{
    private Strategy $strategy;
    public function setUp(): void
    {
        parent::setUp();
        $this->strategy = new Strategy();
    }

    /** @test */
    public function attribute() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $this->assertSame($attribute, $this->strategy->getAttribute());
    }

    /** @test */
    public function value_manager() {
        $value = new ValueManager();
        $this->strategy->setValueManager($value);
        $this->assertSame($value, $this->strategy->getValueManager());
    }

    /** @test */
    public function create_value() {
        $entityKey = 1;
        $domainKey = 2;
        $attrKey = 3;
        $valueToSave = 'test';

        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);

        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);

        $attribute = new Attribute();
        $attribute->setKey($attrKey);
        $attribute->setAttributeSet($attrSet);

        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setRuntime($valueToSave);
        $this->strategy->setValueManager($value);

        $result = $this->strategy->createValue();

        $this->assertEquals(1, ValueStringModel::query()->count());

        $record = ValueStringModel::query()->first();
        $this->assertNotNull($record);
        $this->assertEquals($domainKey, $record->getDomainKey());
        $this->assertEquals($entityKey, $record->getEntityKey());
        $this->assertEquals($attrKey, $record->getAttrKey());
        $this->assertEquals($valueToSave, $record->getVal());

        $this->assertNull($value->getRuntime());
        $this->assertEquals($valueToSave, $value->getStored());
        $this->assertEquals($record->getKey(), $value->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }

    /** @test */
    public function create_value_no_runtime() {
        $entity = new Entity();
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->setAttributeSet($attrSet);
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $this->strategy->setValueManager($value);
        $result = $this->strategy->createValue();
        $this->assertEquals(0, ValueStringModel::query()->count());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function update_value() {
        $valueToSave = 'test';
        $valueKey = 1;

        $record = new ValueStringModel();
        $record->setDomainKey(1)
            ->setEntityKey(2)
            ->setAttrKey(3)
            ->setVal('old');
        $record->save();
        $record->refresh();

        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);

        $value = new ValueManager();
        $value->setKey($valueKey);
        $value->setRuntime($valueToSave);
        $this->strategy->setValueManager($value);

        $result = $this->strategy->updateValue();

        $record = ValueStringModel::query()->first();
        $this->assertEquals($valueToSave, $record->getVal());

        $this->assertNull($value->getRuntime());
        $this->assertEquals($valueToSave, $value->getStored());
        $this->assertEquals($record->getKey(), $value->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }

    /** @test */
    public function update_value_no_runtime() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey(1);
        $this->strategy->setValueManager($value);
        $result = $this->strategy->updateValue();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function delete_value() {
        $record = new ValueStringModel();
        $record->setDomainKey(1)
            ->setEntityKey(2)
            ->setAttrKey(3)
            ->setVal('old')
            ->save();

        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey($record->getKey());
        $value->setStored($record->getVal());
        $value->setRuntime('new');
        $this->strategy->setValueManager($value);
        $result = $this->strategy->deleteValue();

        $this->assertEquals(0, ValueStringModel::query()->count());

        $this->assertNull($value->getRuntime());
        $this->assertNull($value->getStored());
        $this->assertNull($value->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::DELETED->code(), $result->getCode());
        $this->assertEquals(_RESULT::DELETED->message(), $result->getMessage());
    }

    /** @test */
    public function delete_value_no_key() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setRuntime('new');
        $this->strategy->setValueManager($value);
        $result = $this->strategy->deleteValue();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function find_action() {
        $record = new ValueStringModel();
        $record->setDomainKey(1)
            ->setEntityKey(2)
            ->setAttrKey(3)
            ->setVal('test')
            ->save();

        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey($record->getKey());
        $this->strategy->setValueManager($value);

        $result = $this->strategy->findAction();

        $this->assertNull($value->getRuntime());
        $this->assertEquals("test", $value->getStored());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $result->getMessage());
    }

    /** @test */
    public function find_action_no_key() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $this->strategy->setValueManager($value);
        $result = $this->strategy->findAction();
        $this->assertNull($value->getRuntime());
        $this->assertNull($value->getStored());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function find_action_no_record() {
        $attribute = new Attribute();
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey(123);
        $this->strategy->setValueManager($value);
        $result = $this->strategy->findAction();
        $this->assertNull($value->getRuntime());
        $this->assertNull($value->getStored());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::NOT_FOUND->message(), $result->getMessage());
    }

    /** @test */
    public function create_action() {
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['createValue'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('createValue')
            ->willReturn((new Result())->created());
        $result = $strategy->createAction();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }

    /** @test */
    public function create_action_order() {
        $strategy = new StrategyFixture;
        $strategy->createAction();
        $this->assertEquals(['beforeCreate', 'createValue', 'afterCreate'], $strategy->lifecycle);
    }

    /** @test */
    public function update_action() {
        $valueManager = $this->getMockBuilder(ValueManager::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $valueManager->expects($this->once())
            ->method('getKey')
            ->willReturn(1);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['updateValue', 'getValueManager'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('getValueManager')
            ->willReturn($valueManager);
        $strategy->expects($this->once())
            ->method('updateValue')
            ->willReturn((new Result())->updated());
        $result = $strategy->updateAction();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }

    /** @test */
    public function update_action_order() {
        $strategy = new StrategyFixture;
        $value = new ValueManager();
        $strategy->setValueManager($value);
        $strategy->updateAction();
        $this->assertEquals(['beforeUpdate', 'createValue', 'afterUpdate'], $strategy->lifecycle);
    }

    /** @test */
    public function update_action_order_when_existing_value_key() {
        $strategy = new StrategyFixture;
        $value = new ValueManager();
        $value->setKey(123);
        $strategy->setValueManager($value);
        $strategy->updateAction();
        $this->assertEquals(['beforeUpdate', 'updateValue', 'afterUpdate'], $strategy->lifecycle);
    }

    /** @test */
    public function delete_action() {
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['deleteValue'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('deleteValue')
            ->willReturn((new Result())->deleted());
        $result = $strategy->deleteAction();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::DELETED->code(), $result->getCode());
        $this->assertEquals(_RESULT::DELETED->message(), $result->getMessage());
    }

    /** @test */
    public function delete_action_order() {
        $strategy = new StrategyFixture;
        $value = new ValueManager();
        $strategy->setValueManager($value);
        $strategy->deleteAction();
        $this->assertEquals(['beforeDelete', 'deleteValue', 'afterDelete'], $strategy->lifecycle);
    }

    /** @test */
    public function is_create() {
        $this->assertTrue($this->strategy->isCreate());
        $this->strategy->create = false;
        $this->assertFalse($this->strategy->isCreate());
    }

    /** @test */
    public function no_create() {
        $this->strategy->create = false;
        $entity = new Entity();
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->setAttributeSet($attrSet);
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey(123);
        $this->strategy->setValueManager($value);
        $result = $this->strategy->createValue();
        $this->assertFalse($value->isRuntime());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_ALLOWED->code(), $result->getCode());
        $this->assertEquals(_RESULT::NOT_ALLOWED->message(), $result->getMessage());
    }

    /** @test */
    public function is_update() {
        $this->assertTrue($this->strategy->isUpdate());
        $this->strategy->update = false;
        $this->assertFalse($this->strategy->isUpdate());
    }

    /** @test */
    public function no_update() {
        $this->strategy->update = false;
        $entity = new Entity();
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->setAttributeSet($attrSet);
        $this->strategy->setAttribute($attribute);
        $value = new ValueManager();
        $value->setKey(123);
        $this->strategy->setValueManager($value);
        $result = $this->strategy->updateValue();
        $this->assertFalse($value->isRuntime());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_ALLOWED->code(), $result->getCode());
        $this->assertEquals(_RESULT::NOT_ALLOWED->message(), $result->getMessage());
    }

    /** @test */
    public function save_action_create()
    {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getKey')
            ->willReturn(null);
        $attributeSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $attributeSet->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $attribute = $this->getMockBuilder(Attribute::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $attribute->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attributeSet);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['getAttribute', 'createAction'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('getAttribute')
            ->willReturn($attribute);
        $strategy->expects($this->once())
            ->method('createAction')
            ->willReturn((new Result())->created());
        $result = $strategy->saveAction();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }

    /** @test */
    public function save_action_update()
    {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getKey')
            ->willReturn(1);
        $attributeSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $attributeSet->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $attribute = $this->getMockBuilder(Attribute::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $attribute->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attributeSet);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['getAttribute', 'updateAction'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('getAttribute')
            ->willReturn($attribute);
        $strategy->expects($this->once())
            ->method('updateAction')
            ->willReturn((new Result())->updated());
        $result = $strategy->saveAction();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }

    /** @test */
    public function default_value_rule() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $strategy = new Strategy();
        $strategy->setAttribute($attribute);
        $this->assertEquals(
            ATTR_TYPE::INTEGER->validationRule(),
            $strategy->getDefaultValueRule()
        );

        $attribute->setType(ATTR_TYPE::TEXT->value());
        $this->assertEquals(
            ATTR_TYPE::TEXT->validationRule(),
            $strategy->getDefaultValueRule()
        );
    }

    /** @test */
    public function validation_rules() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $strategy = new Strategy();
        $strategy->setAttribute($attribute);
        $this->assertEquals(
            [
                _VALUE::ENTITY_ID->column() => ['required', 'integer'],
                _VALUE::DOMAIN_ID->column() => ['required','integer'],
                _VALUE::ATTRIBUTE_ID->column() => ['required','integer'],
                _VALUE::VALUE->column() => $strategy->getDefaultValueRule()
            ],
            $strategy->getRules()
        );
    }

    /** @test */
    public function validation_data() {
        $entity = new Entity();
        $entity->setDomainKey(4);
        $entity->setKey(3);
        $attrSet = new AttributeSet();
        $attrSet->setKey(2);
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->setAttributeSet($attrSet);
        $attribute->setKey(1);
        $strategy = new Strategy();
        $strategy->setAttribute($attribute);
        $valueManager = new ValueManager();
        $valueManager->setRuntime('test');
        $strategy->setValueManager($valueManager);
        $this->assertEquals(
            [
                _VALUE::ENTITY_ID->column() => $entity->getKey(),
                _VALUE::DOMAIN_ID->column() => $entity->getDomainKey(),
                _VALUE::ATTRIBUTE_ID->column() => $attribute->getKey(),
                _VALUE::VALUE->column() => $valueManager->getRuntime()
            ],
            $strategy->getValidatedData()
        );
    }

    /** @test */
    public function validator() {
        $entity = new Entity();
        $entity->setDomainKey(4);
        $entity->setKey(3);
        $attrSet = new AttributeSet();
        $attrSet->setKey(2);
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->setAttributeSet($attrSet);
        $attribute->setKey(1);
        $attribute->setType(ATTR_TYPE::STRING->value());
        $strategy = new Strategy();
        $strategy->setAttribute($attribute);
        $valueManager = new ValueManager();
        $valueManager->setRuntime('test');
        $strategy->setValueManager($valueManager);

        $validator = $strategy->getValidator();
        $this->assertEquals($strategy->getRules(), $validator->getRules());
        $this->assertEquals($strategy->getValidatedData(), $validator->getData());
    }
}