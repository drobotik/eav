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
use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\ValueBase;
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
     * @covers \Drobotik\Eav\Value\ValueAction::create
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
        $valueModel = $this->makeValueModel();
        $valueRecord = $valueModel->find(
            ATTR_TYPE::STRING->valueTable(),
            $domainKey,
            $entityKey,
            $attrKey
        );
        $this->assertIsArray($valueRecord);
        $valueKey = $valueRecord[_VALUE::ID->column()];
        $this->assertEquals($valueToSave, $valueRecord[_VALUE::VALUE->column()]);

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals($valueToSave, $valueManager->getStored());
        $this->assertEquals($valueKey, $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueAction::create
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

        $test = Connection::get()->createQueryBuilder()
            ->select('*')->from(ATTR_TYPE::STRING->valueTable())
            ->executeQuery()->fetchAssociative();
        $this->assertFalse($test);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueAction::find
     */
    public function find() {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $attrKey = $this->eavFactory->createAttribute($domainKey);
        $attrRecord = Connection::get()->createQueryBuilder()->select('*')
            ->from(_ATTR::table())->executeQuery()->fetchAssociative();
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attrKey);

        $valueModel = new ValueBase();
        $valueKey = $valueModel->create(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $attrKey,"test");

        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);
        $attrSet = new AttributeSet();
        $attrSet->setKey($setKey);
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->getBag()->setFields($attrRecord);
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
        $this->assertEquals($valueKey, $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueAction::find
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
     * @covers \Drobotik\Eav\Value\ValueAction::find
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
     * @covers \Drobotik\Eav\Value\ValueAction::update
     */
    public function update_value() {
        $valueToSave = 'new';
        $domainKey = 1;
        $entityKey = 2;
        $attrKey = 2;
        $attrSetKey = 4;

        $valueModel = $this->makeValueModel();
        $valueKey = $valueModel->create(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $attrKey, 'old');

        $valueManager = new ValueManager();
        $valueManager->setKey($valueKey);
        $valueManager->setRuntime($valueToSave);

        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);

        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);
        $attrSet->setKey(4);

        $attribute = new Attribute();
        $attribute->setKey($attrKey);

        $container = new AttributeContainer();
        $container
            ->setAttribute($attribute)
            ->setAttributeSet($attrSet)
            ->setValueManager($valueManager);

        $this->action->setAttributeContainer($container);
        $result = $this->action->update();

        $record = $valueModel->find(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $attrKey);
        $this->assertIsArray($record);
        $this->assertEquals($valueToSave, $record[_VALUE::VALUE->column()]);

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals($valueToSave, $valueManager->getStored());
        $this->assertEquals($valueKey, $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueAction::update
     */
    public function update_value_no_runtime() {
        $container = new AttributeContainer();
        $valueManager = new ValueManager();
        $valueManager->setKey(1);
        $entity = new Entity();
        $entity->setKey(2);
        $entity->setDomainKey(3);

        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);
        $attrSet->setKey(3);

        $container
            ->makeAttribute()
            ->setAttributeSet($attrSet)
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
     * @covers \Drobotik\Eav\Value\ValueAction::delete
     */
    public function delete_value() {
        $domainKey = 1;
        $entityKey = 2;
        $attrKey = 2;
        $valueModel = $this->makeValueModel();
        $valueKey = $valueModel->create(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $attrKey, 'test');

        $value = new ValueManager();
        $value->setKey($valueKey);
        $value->setStored('test');
        $value->setRuntime('new');

        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);

        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);

        $attribute = new Attribute();
        $attribute->setKey($attrKey);

        $container = new AttributeContainer();
        $container
            ->setAttribute($attribute)
            ->setAttributeSet($attrSet)
            ->setValueManager($value);
        $this->action->setAttributeContainer($container);

        $result = $this->action->delete();

        $record = $valueModel->find(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $attrKey);
        $this->assertFalse($record);

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
     * @covers \Drobotik\Eav\Value\ValueAction::delete
     */
    public function delete_value_no_key() {
        $domainKey = 1;
        $entityKey = 2;
        $attrKey = 2;
        $valueManager = new ValueManager();
        $valueManager->setRuntime('new');

        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);

        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);

        $attribute = new Attribute();
        $attribute->setKey($attrKey);

        $container = new AttributeContainer();
        $container->makeAttribute()
            ->setAttribute($attribute)
            ->setAttributeSet($attrSet)
            ->setValueManager($valueManager);
        $this->action->setAttributeContainer($container);
        $result = $this->action->delete();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(_RESULT::EMPTY->message(), $result->getMessage());
    }
}