<?php

namespace Tests\Unit\Strategy;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\VALUE_RESULT;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Result\ValueResult;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\ValueManager;
use Tests\TestCase;

class StrategyTest extends TestCase
{
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

        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::CREATED->message(), $result->getMessage());
    }
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
        $this->assertInstanceOf(ValueResult::class, $result);
        $this->assertEquals(VALUE_RESULT::EMPTY->code(), $result->getCode());
        $this->assertEquals(VALUE_RESULT::EMPTY->message(), $result->getMessage());
    }



}