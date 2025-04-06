<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueAction;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Value\ValueAction;
use Kuperwood\Eav\Value\ValueManager;
use PDO;
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
     * @covers \Kuperwood\Eav\Value\ValueAction::create
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
            ATTR_TYPE::STRING,
            $domainKey,
            $entityKey,
            $attrKey
        );
        $this->assertIsArray($valueRecord);
        $valueKey = $valueRecord[_VALUE::ID];
        $this->assertEquals($valueToSave, $valueRecord[_VALUE::VALUE]);

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals($valueToSave, $valueManager->getStored());
        $this->assertEquals($valueKey, $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueAction::create
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

        $table = ATTR_TYPE::valueTable(ATTR_TYPE::STRING);

        $sql = "SELECT * FROM `$table` LIMIT 1";
        $stmt = Connection::get()->prepare($sql);
        $stmt->execute();
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertFalse($test);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::EMPTY, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EMPTY), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueAction::find
     */
    public function find() {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $attrKey = $this->eavFactory->createAttribute($domainKey);

        $pdo = Connection::get();
        $table = _ATTR::table();
        $sql = "SELECT * FROM `$table` LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $attrRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attrKey);

        $valueModel = new ValueBase();
        $valueKey = $valueModel->create(ATTR_TYPE::STRING, $domainKey, $entityKey, $attrKey,"test");

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
        $this->assertEquals(_RESULT::FOUND, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::FOUND), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueAction::find
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
        $this->assertEquals(_RESULT::EMPTY, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EMPTY), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueAction::find
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
        $this->assertEquals(_RESULT::NOT_FOUND, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_FOUND), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueAction::update
     */
    public function update_value() {
        $valueToSave = 'new';
        $domainKey = 1;
        $entityKey = 2;
        $attrKey = 2;
        $attrSetKey = 4;

        $valueModel = $this->makeValueModel();
        $valueKey = $valueModel->create(ATTR_TYPE::STRING, $domainKey, $entityKey, $attrKey, 'old');

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

        $record = $valueModel->find(ATTR_TYPE::STRING, $domainKey, $entityKey, $attrKey);
        $this->assertIsArray($record);
        $this->assertEquals($valueToSave, $record[_VALUE::VALUE]);

        $this->assertNull($valueManager->getRuntime());
        $this->assertEquals($valueToSave, $valueManager->getStored());
        $this->assertEquals($valueKey, $valueManager->getKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::UPDATED), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueAction::update
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
        $this->assertEquals(_RESULT::EMPTY, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EMPTY), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueAction::delete
     */
    public function delete_value() {
        $domainKey = 1;
        $entityKey = 2;
        $attrKey = 2;
        $valueModel = $this->makeValueModel();
        $valueKey = $valueModel->create(ATTR_TYPE::STRING, $domainKey, $entityKey, $attrKey, 'test');

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

        $record = $valueModel->find(ATTR_TYPE::STRING, $domainKey, $entityKey, $attrKey);
        $this->assertFalse($record);

        $this->assertNull($value->getRuntime());
        $this->assertNull($value->getStored());
        $this->assertFalse($value->hasKey());

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::DELETED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::DELETED), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueAction::delete
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
        $this->assertEquals(_RESULT::EMPTY, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::EMPTY), $result->getMessage());
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Value\ValueAction::delete
     */
    public function delete_not_deleted_status() {
        $valueModel = $this->getMockBuilder(ValueBase::class)
            ->onlyMethods(['destroy'])->getMock();
        $valueModel->method('destroy')->willReturn(0);

        $valueAction = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['makeValueModel'])->getMock();
        $valueAction->method('makeValueModel')->willReturn($valueModel);

        $valueManager = new ValueManager();
        $valueManager->setRuntime('new');
        $valueManager->setKey(1);

        $entity = new Entity();
        $entity->setKey(1);
        $entity->setDomainKey(2);

        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);

        $attribute = new Attribute();
        $attribute->setKey(3);

        $container = new AttributeContainer();
        $container->makeAttribute()
            ->setAttribute($attribute)
            ->setAttributeSet($attrSet)
            ->setValueManager($valueManager)
            ->setValueAction($valueAction);

        $result = $valueAction->delete();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_DELETED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_DELETED), $result->getMessage());
    }
}