<?php

namespace Tests\Unit\Strategy;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EavContainer;
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
    public function create_value() {
        $entityKey = 1;
        $domainKey = 2;
        $attrKey = 3;
        $valueToSave = 'test';
        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);
        $attrSet = new AttributeSet();
        $attribute = new Attribute();
        $attribute->setKey($attrKey);
        $valueManager = new ValueManager();
        $valueManager->setRuntime($valueToSave);
        $container = new EavContainer();
        $container->setEntity($entity)
            ->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->setValueManager($valueManager);
        $this->strategy->setEavContainer($container);
        $result = $this->strategy->createValue();

        $this->assertEquals(1, ValueStringModel::query()->count());

        $record = ValueStringModel::query()->first();
        $this->assertNotNull($record);
        $this->assertEquals($domainKey, $record->getDomainKey());
        $this->assertEquals($entityKey, $record->getEntityKey());
        $this->assertEquals($attrKey, $record->getAttrKey());
        $this->assertEquals($valueToSave, $record->getVal());

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals($valueToSave, $valueManager->getStored());
        $this->assertEquals($record->getKey(), $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }

    /** @test */
    public function create_value_no_runtime() {
        $container = new EavContainer();
        $container->makeEntity()
            ->makeAttributeSet()
            ->makeAttribute()
            ->makeValueManager()
            ->setStrategy($this->strategy);
        $result = $this->strategy->createValue();
        $this->assertEquals(0, ValueStringModel::query()->count());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function update_value() {
        $valueToSave = 'new';
        $valueKey = 1;
        $record = new ValueStringModel();
        $record->setDomainKey(1)
            ->setEntityKey(2)
            ->setAttrKey(3)
            ->setVal('old');
        $record->save();
        $record->refresh();

        $valueManager = new ValueManager();
        $valueManager->setKey($valueKey);
        $valueManager->setRuntime($valueToSave);

        $container = new EavContainer();
        $container->makeAttribute()
            ->setValueManager($valueManager);
        $this->strategy->setEavContainer($container);
        $result = $this->strategy->updateValue();

        $record = ValueStringModel::query()->first();
        $this->assertEquals($valueToSave, $record->getVal());

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals($valueToSave, $valueManager->getStored());
        $this->assertEquals($record->getKey(), $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }

    /** @test */
    public function update_value_no_runtime() {
        $container = new EavContainer();
        $valueManager = new ValueManager();
        $valueManager->setKey(1);
        $container
            ->makeAttribute()
            ->setValueManager($valueManager);
        $this->strategy->setEavContainer($container);
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
        $value = new ValueManager();
        $value->setKey($record->getKey());
        $value->setStored($record->getVal());
        $value->setRuntime('new');
        $container = new EavContainer();
        $container->makeAttribute()
            ->setValueManager($value);
        $this->strategy->setEavContainer($container);

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
        $valueManager = new ValueManager();
        $valueManager->setRuntime('new');
        $container = new EavContainer();
        $container->makeAttribute()
            ->setValueManager($valueManager);
        $this->strategy->setEavContainer($container);
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
        $valueManager = new ValueManager();
        $valueManager->setKey($record->getKey());
        $container = new EavContainer();
        $container->makeAttribute()
            ->setValueManager($valueManager);

        $this->strategy->setEavContainer($container);

        $result = $this->strategy->findAction();

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals("test", $valueManager->getStored());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $result->getMessage());
    }

    /** @test */
    public function find_action_no_key() {
        $container = new EavContainer();
        $container->makeAttribute()
            ->makeValueManager();
        $this->strategy->setEavContainer($container);
        $result = $this->strategy->findAction();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }

    /** @test */
    public function find_action_no_record() {
        $valueManager = new ValueManager();
        $valueManager->setKey(123);
        $container = new EavContainer();
        $container->makeAttribute()
            ->setValueManager($valueManager);
        $this->strategy->setEavContainer($container);
        $result = $this->strategy->findAction();
        $this->assertNull($valueManager->getRuntime());
        $this->assertNull($valueManager->getStored());
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
        $container = $this->getMockBuilder(EavContainer::class)
            ->onlyMethods(['getValueManager'])
            ->getMock();
        $container->expects($this->once())
            ->method('getValueManager')
            ->willReturn($valueManager);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['updateValue', 'getEavContainer'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('getEavContainer')
            ->willReturn($container);
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
        $container = new EavContainer();
        $container->makeValueManager();
        $strategy->setEavContainer($container);
        $strategy->updateAction();
        $this->assertEquals(['beforeUpdate', 'createValue', 'afterUpdate'], $strategy->lifecycle);
    }

    /** @test */
    public function update_action_order_when_existing_value_key() {
        $strategy = new StrategyFixture;
        $valueManager = new ValueManager();
        $valueManager->setKey(123);
        $container = new EavContainer();
        $container->setValueManager($valueManager);
        $strategy->setEavContainer($container);
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
        $container = new EavContainer();
        $container->makeValueManager();
        $strategy->setEavContainer($container);
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
        $valueManager = new ValueManager();
        $valueManager->setKey(123);
        $container = new EavContainer();
        $container->makeEntity()
            ->makeAttributeSet()
            ->makeAttribute()
            ->setValueManager($valueManager);
        $this->strategy->setEavContainer($container);
        $result = $this->strategy->createValue();
        $this->assertFalse($valueManager->isRuntime());
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
        $valueManager = new ValueManager();
        $valueManager->setKey(123);
        $container = new EavContainer();
        $container->makeEntity()
            ->makeAttributeSet()
            ->makeAttribute()
            ->setValueManager($valueManager);
        $this->strategy->setEavContainer($container);
        $result = $this->strategy->updateValue();
        $this->assertFalse($valueManager->isRuntime());
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
        $container = $this->getMockBuilder(EavContainer::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $container->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['createAction'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('createAction')
            ->willReturn((new Result())->created());
        $strategy->setEavContainer($container);
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
        $container = $this->getMockBuilder(EavContainer::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $container->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['updateAction'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('updateAction')
            ->willReturn((new Result())->updated());
        $strategy->setEavContainer($container);
        $result = $strategy->saveAction();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }

    /** @test */
    public function default_value_rule() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $container = new EavContainer();
        $container->setAttribute($attribute);
        $this->strategy->setEavContainer($container);
        $this->assertEquals(
            ATTR_TYPE::INTEGER->validationRule(),
            $this->strategy->getDefaultValueRule()
        );
        $attribute->setType(ATTR_TYPE::TEXT->value());
        $this->assertEquals(
            ATTR_TYPE::TEXT->validationRule(),
            $this->strategy->getDefaultValueRule()
        );
    }

    /** @test */
    public function validation_rules() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $container = new EavContainer();
        $container->setAttribute($attribute);
        $this->strategy->setEavContainer($container);
        $this->assertEquals(
            [
                _VALUE::ENTITY_ID->column() => ['required', 'integer'],
                _VALUE::DOMAIN_ID->column() => ['required','integer'],
                _VALUE::ATTRIBUTE_ID->column() => ['required','integer'],
                _VALUE::VALUE->column() => $this->strategy->getDefaultValueRule()
            ],
            $this->strategy->getRules()
        );
    }

    /** @test */
    public function validation_rules_with_custom_rule() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $container = new EavContainer();
        $container->setAttribute($attribute);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['rules'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('rules')
            ->willReturn(['new_rule']);
        $strategy->setEavContainer($container);
        $strategy->setEavContainer($container);
        $result = $strategy->getRules();
        $this->assertEquals(['new_rule'], $result[_VALUE::VALUE->column()]);
    }

    /** @test */
    public function validation_data() {
        $entity = new Entity();
        $entity->setDomainKey(4);
        $entity->setKey(3);
        $attrSet = new AttributeSet();
        $attrSet->setKey(2);
        $attribute = new Attribute();
        $attribute->setKey(1);
        $valueManager = new ValueManager();
        $valueManager->setRuntime('test');
        $container = new EavContainer();
        $container->setEntity($entity)
            ->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->setValueManager($valueManager);
        $this->strategy->setEavContainer($container);
        $this->assertEquals(
            [
                _VALUE::ENTITY_ID->column() => $entity->getKey(),
                _VALUE::DOMAIN_ID->column() => $entity->getDomainKey(),
                _VALUE::ATTRIBUTE_ID->column() => $attribute->getKey(),
                _VALUE::VALUE->column() => $valueManager->getRuntime()
            ],
            $this->strategy->getValidatedData()
        );
    }

    /** @test */
    public function validator() {
        $entity = new Entity();
        $entity->setDomainKey(4);
        $entity->setKey(3);
        $attrSet = new AttributeSet();
        $attrSet->setKey(2);
        $attribute = new Attribute();
        $attribute->setKey(1);
        $attribute->setType(ATTR_TYPE::STRING->value());
        $valueManager = new ValueManager();
        $valueManager->setRuntime('test');
        $container = new EavContainer();
        $container->setEntity($entity)
            ->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->setValueManager($valueManager);
        $this->strategy->setEavContainer($container);
        $validator = $this->strategy->getValidator();
        $this->assertEquals($this->strategy->getRules(), $validator->getRules());
        $this->assertEquals($this->strategy->getValidatedData(), $validator->getData());
    }

    /** @test */
    public function validate_fails_action() {
        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fails', 'errors'])
            ->getMock();

        $validator->expects($this->once())
            ->method('fails')
            ->willReturn(true);

        $messageBag = new MessageBag();
        $messageBag->add('test', 'test');

        $validator->expects($this->once())
            ->method('errors')
            ->willReturn($messageBag);

        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['getValidator'])
            ->getMock();

        $strategy->expects($this->once())
            ->method('getValidator')
            ->willReturn($validator);

        $container = new EavContainer();
        $entity = new Entity();
        $entity->setDomainKey(4);
        $entity->setKey(3);
        $attrSet = new AttributeSet();
        $attrSet->setKey(2);
        $attribute = new Attribute();
        $attribute->setKey(1);
        $attribute->setType(ATTR_TYPE::STRING->value());
        $valueManager = new ValueManager();
        $valueManager->setRuntime('test');
        $container->setEntity($entity)
            ->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->setValueManager($valueManager);

        $strategy->setEavContainer($container);
        $result = $strategy->validateAction();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_FAILS->code(), $result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_FAILS->message(), $result->getMessage());
        $this->assertSame($messageBag, $result->getData());
    }

    /** @test */
    public function validate_passed_action() {
        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fails', 'errors'])
            ->getMock();
        $validator->expects($this->once())
            ->method('fails')
            ->willReturn(false);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['getValidator'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('getValidator')
            ->willReturn($validator);

        $container = new EavContainer();
        $entity = new Entity();
        $entity->setDomainKey(4);
        $entity->setKey(3);
        $attrSet = new AttributeSet();
        $attrSet->setKey(2);
        $attribute = new Attribute();
        $attribute->setKey(1);
        $attribute->setType(ATTR_TYPE::STRING->value());
        $valueManager = new ValueManager();
        $valueManager->setRuntime('test');
        $container->setEntity($entity)
            ->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->setValueManager($valueManager);

        $strategy->setEavContainer($container);
        $result = $strategy->validateAction();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_PASSED->code(), $result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_PASSED->message(), $result->getMessage());
        $this->assertNull($result->getData());
    }
}