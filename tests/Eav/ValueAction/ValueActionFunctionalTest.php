<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueAction;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Value\ValueAction;
use Drobotik\Eav\Value\ValueManager;
use Tests\TestCase;


class ValueActionFunctionalTest extends TestCase
{
    private ValueAction $action;

    public function setUp(): void
    {
        parent::setUp();
        $this->action = new ValueAction;
    }
    /**
     * @test
     * @group functional
     * @covers ValueAction::create
     */
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
        $valueManager = new ValueManager();
        $valueManager->setRuntime($valueToSave);
        $container = new AttributeContainer();
        $container->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->setValueManager($valueManager);
        $this->action->setAttributeContainer($container);
        $result = $this->action->create();

        $this->assertEquals(1, ValueStringModel::query()->count());

        $record = ValueStringModel::query()->first();
        $this->assertNotNull($record);
        $this->assertEquals($domainKey, $record->getDomainKey());
        $this->assertEquals($entityKey, $record->getEntityKey());
        $this->assertEquals($attrKey, $record->getAttrKey());
        $this->assertEquals($valueToSave, $record->getValue());

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals($valueToSave, $valueManager->getStored());
        $this->assertEquals($record->getKey(), $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers ValueAction::create
     */
    public function create_value_no_runtime() {
        $entity = new Entity();
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);
        $container = new AttributeContainer();
        $container
            ->setAttributeSet($attrSet)
            ->setValueAction($this->action)
            ->makeAttribute()
            ->makeValueManager();

        $result = $this->action->create();

        $this->assertEquals(0, ValueStringModel::query()->count());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers ValueAction::find
     */
    public function find() {
        $domainModel = $this->eavFactory->createDomain();
        $entityModel = $this->eavFactory->createEntity($domainModel);
        $setModel = $this->eavFactory->createAttributeSet($domainModel);
        $groupModel = $this->eavFactory->createGroup($setModel);
        $attributeModel = $this->eavFactory->createAttribute($domainModel);
        $this->eavFactory->createPivot($domainModel, $setModel, $groupModel, $attributeModel);
        $valueModel = $this->eavFactory->createValue(
            ATTR_TYPE::STRING, $domainModel, $entityModel, $attributeModel, "test");

        $entity = new Entity();
        $entity->setKey($entityModel->getKey());
        $entity->setDomainKey($domainModel->getKey());
        $attrSet = new AttributeSet();
        $attrSet->setKey($setModel->getKey());
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->getBag()->setFields($attributeModel->toArray());
        $valueManager = new ValueManager();
        $container = new AttributeContainer();
        $container
            ->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->setValueManager($valueManager);
        $this->action->setAttributeContainer($container);

        $result = $this->action->find();

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals("test", $valueManager->getStored());
        $this->assertEquals($valueModel->getKey(), $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers ValueAction::find
     */
    public function find_no_keys() {
        $entity = new Entity();
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);
        $container = new AttributeContainer();
        $container
            ->setAttributeSet($attrSet)
            ->makeAttribute()
            ->makeValueManager();
        $this->action->setAttributeContainer($container);
        $result = $this->action->find();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers ValueAction::find
     */
    public function find_not_found() {
        $entity = new Entity();
        $entity->setKey(1)
            ->setDomainKey(2);
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->setKey(3);
        $valueManager = new ValueManager();
        $container = new AttributeContainer();
        $container
            ->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->setValueManager($valueManager);
        $this->action->setAttributeContainer($container);

        $result = $this->action->find();

        $this->assertFalse($valueManager->hasKey());
        $this->assertNull($valueManager->getRuntime());
        $this->assertNull($valueManager->getStored());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::NOT_FOUND->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers ValueAction::update
     */
    public function update_value() {
        $valueToSave = 'new';
        $valueKey = 1;
        $record = new ValueStringModel();
        $record->setDomainKey(1)
            ->setEntityKey(2)
            ->setAttrKey(3)
            ->setValue('old');
        $record->save();
        $record->refresh();

        $valueManager = new ValueManager();
        $valueManager->setKey($valueKey);
        $valueManager->setRuntime($valueToSave);

        $container = new AttributeContainer();
        $container->makeAttribute()
            ->setValueManager($valueManager);
        $this->action->setAttributeContainer($container);
        $result = $this->action->update();

        $record = ValueStringModel::query()->first();
        $this->assertEquals($valueToSave, $record->getValue());

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals($valueToSave, $valueManager->getStored());
        $this->assertEquals($record->getKey(), $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers ValueAction::update
     */
    public function update_value_no_runtime() {
        $container = new AttributeContainer();
        $valueManager = new ValueManager();
        $valueManager->setKey(1);
        $container
            ->makeAttribute()
            ->setValueManager($valueManager);
        $this->action->setAttributeContainer($container);
        $result = $this->action->update();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers ValueAction::delete
     */
    public function delete_value() {
        $record = new ValueStringModel();
        $record->setDomainKey(1)
            ->setEntityKey(2)
            ->setAttrKey(3)
            ->setValue('old')
            ->save();
        $value = new ValueManager();
        $value->setKey($record->getKey());
        $value->setStored($record->getValue());
        $value->setRuntime('new');
        $container = new AttributeContainer();
        $container->makeAttribute()
            ->setValueManager($value);
        $this->action->setAttributeContainer($container);

        $result = $this->action->delete();

        $this->assertEquals(0, ValueStringModel::query()->count());

        $this->assertNull($value->getRuntime());
        $this->assertNull($value->getStored());
        $this->assertFalse($value->hasKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::DELETED->code(), $result->getCode());
        $this->assertEquals(_RESULT::DELETED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers ValueAction::delete
     */
    public function delete_value_no_key() {
        $valueManager = new ValueManager();
        $valueManager->setRuntime('new');
        $container = new AttributeContainer();
        $container->makeAttribute()
            ->setValueManager($valueManager);
        $this->action->setAttributeContainer($container);
        $result = $this->action->delete();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }
}