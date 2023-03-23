<?php

namespace Tests\Unit\Models;

use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\AttributeGroupModel;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Model\DomainModel;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\PivotModel;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Model\ValueDatetimeModel;
use Kuperwood\Eav\Model\ValueDecimalModel;
use Kuperwood\Eav\Model\ValueIntegerModel;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Model\ValueTextModel;
use PHPUnit\Framework\TestCase;

class StaticCallsTest extends TestCase
{
    /** @test */
    public function domain() {
        $model = new DomainModel();
        $model->setName('test');
        $this->assertEquals('test', $model->getName());
    }

    /** @test */
    public function entity() {
        $model = new EntityModel();
        $model->setDomainKey(123);
        $this->assertEquals(123, $model->getDomainKey());
    }

    /** @test */
    public function attributeSet() {
        $model = new AttributeSetModel();
        $model->setName("test");
        $this->assertEquals("test", $model->getName());
    }

    /** @test */
    public function attributeGroup() {
        $model = new AttributeGroupModel();
        $model->setName("test")
            ->setAttrSetKey(123);
        $this->assertEquals("test", $model->getName());
        $this->assertEquals(123, $model->getAttrSetKey());
    }

    /** @test */
    public function attribute() {
        $model = new AttributeModel();
        $model->setName("test")
            ->setDomainKey(123)
            ->setType("string")
            ->setDescription("description")
            ->setDefaultValue("default")
            ->setSource("source")
            ->setStrategy("strategy");
        $this->assertEquals(123, $model->getDomainKey());
        $this->assertEquals("test", $model->getName());
        $this->assertEquals("string", $model->getType());
        $this->assertEquals("description", $model->getDescription());
        $this->assertEquals("default", $model->getDefaultValue());
        $this->assertEquals("source", $model->getSource());
        $this->assertEquals("strategy", $model->getStrategy());
    }

    /** @test */
    public function pivot() {
        $model = new PivotModel();
        $model->setDomainKey(123)
            ->setAttrSetKey(456)
            ->setGroupKey(789)
            ->setAttrKey(101);
        $this->assertEquals(123, $model->getDomainKey());
        $this->assertEquals(456, $model->getAttrSetKey());
        $this->assertEquals(789, $model->getAttrGroupKey());
        $this->assertEquals(101, $model->getAttrKey());
    }

    /** @test */
    public function value() {
        $model = new ValueBase();
        $model->setDomainKey(123)
            ->setEntityKey(456)
            ->setAttrKey(789)
            ->setVal("test");
        $this->assertEquals(123, $model->getDomainKey());
        $this->assertEquals(456, $model->getEntityKey());
        $this->assertEquals(789, $model->getAttrKey());
        $this->assertEquals("test", $model->getVal());
    }

    /** @test */
    public function value_string() {
        $model = new ValueStringModel();
        $this->assertEquals(ATTR_TYPE::STRING->valueTable(), $model->getTable());
    }

    /** @test */
    public function value_text() {
        $model = new ValueTextModel();
        $this->assertEquals(ATTR_TYPE::TEXT->valueTable(), $model->getTable());
    }

    /** @test */
    public function value_integer() {
        $model = new ValueIntegerModel();
        $this->assertEquals(ATTR_TYPE::INTEGER->valueTable(), $model->getTable());
    }

    /** @test */
    public function value_datetime() {
        $model = new ValueDatetimeModel();
        $this->assertEquals(ATTR_TYPE::DATETIME->valueTable(), $model->getTable());
    }

    /** @test */
    public function value_decimal() {
        $model = new ValueDecimalModel();
        $this->assertEquals(ATTR_TYPE::DECIMAL->valueTable(), $model->getTable());
    }
}