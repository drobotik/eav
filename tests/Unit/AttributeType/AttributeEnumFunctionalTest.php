<?php

declare(strict_types=1);

namespace Tests\Unit\AttributeType;

use Doctrine\DBAL\Types\Types;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\ValueDatetimeModel;
use Kuperwood\Eav\Model\ValueDecimalModel;
use Kuperwood\Eav\Model\ValueIntegerModel;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Model\ValueTextModel;
use Tests\TestCase;

class AttributeEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers ATTR_TYPE::value
     */
    public function value() {
        $this->assertEquals("int", ATTR_TYPE::INTEGER->value());
        $this->assertEquals("datetime", ATTR_TYPE::DATETIME->value());
        $this->assertEquals("decimal", ATTR_TYPE::DECIMAL->value());
        $this->assertEquals("varchar", ATTR_TYPE::STRING->value());
        $this->assertEquals("text", ATTR_TYPE::TEXT->value());
        $this->assertEquals("manual", ATTR_TYPE::MANUAL->value());
    }
    /**
     * @test
     * @group functional
     * @covers ATTR_TYPE::isValid
     */
    public function is_valid() {
        $this->assertTrue(ATTR_TYPE::isValid("int"));
        $this->assertTrue(ATTR_TYPE::isValid("datetime"));
        $this->assertTrue(ATTR_TYPE::isValid("decimal"));
        $this->assertTrue(ATTR_TYPE::isValid("varchar"));
        $this->assertTrue(ATTR_TYPE::isValid("text"));
        $this->assertFalse(ATTR_TYPE::isValid("manual"));
        $this->assertFalse(ATTR_TYPE::isValid("test"));
    }
    /**
     * @test
     * @group functional
     * @covers ATTR_TYPE::valueTable
     */
    public function value_table() {
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::INTEGER->value()), ATTR_TYPE::INTEGER->valueTable());
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::DATETIME->value()), ATTR_TYPE::DATETIME->valueTable());
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::DECIMAL->value()), ATTR_TYPE::DECIMAL->valueTable());
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::STRING->value()), ATTR_TYPE::STRING->valueTable());
        $this->assertEquals(sprintf(_VALUE::table(), ATTR_TYPE::TEXT->value()), ATTR_TYPE::TEXT->valueTable());
    }
    /**
     * @test
     * @group functional
     * @covers ATTR_TYPE::model
     */
    public function model() {
        $this->assertInstanceOf(ValueIntegerModel::class, ATTR_TYPE::INTEGER->model());
        $this->assertInstanceOf(ValueDatetimeModel::class, ATTR_TYPE::DATETIME->model());
        $this->assertInstanceOf(ValueDecimalModel::class, ATTR_TYPE::DECIMAL->model());
        $this->assertInstanceOf(ValueStringModel::class, ATTR_TYPE::STRING->model());
        $this->assertInstanceOf(ValueTextModel::class, ATTR_TYPE::TEXT->model());
    }
    /**
     * @test
     * @group functional
     * @covers ATTR_TYPE::doctrineType
     */
    public function doctrine_type() {
        $this->assertEquals(Types::INTEGER, ATTR_TYPE::INTEGER->doctrineType());
        $this->assertEquals(Types::DATETIME_MUTABLE, ATTR_TYPE::DATETIME->doctrineType());
        $this->assertEquals(Types::DECIMAL, ATTR_TYPE::DECIMAL->doctrineType());
        $this->assertEquals(Types::STRING, ATTR_TYPE::STRING->doctrineType());
        $this->assertEquals(Types::TEXT, ATTR_TYPE::TEXT->doctrineType());
    }
    /**
     * @test
     * @group functional
     * @covers ATTR_TYPE::validationRule
     */
    public function validation_rule() {
        $this->assertEquals(["integer"], ATTR_TYPE::INTEGER->validationRule());
        $this->assertEquals(["date"], ATTR_TYPE::DATETIME->validationRule());
        $this->assertEquals(["regex:/^[0-9]{1,11}(?:\.[0-9]{1,3})?$/"], ATTR_TYPE::DECIMAL->validationRule());
        $this->assertEquals(["string","min:1","max:191"], ATTR_TYPE::STRING->validationRule());
        $this->assertEquals(["min:1","max:1000"], ATTR_TYPE::TEXT->validationRule());
    }
    /**
     * @test
     * @group functional
     * @covers ATTR_TYPE::getCase
     */
    public function get_case() {
        $this->assertEquals(ATTR_TYPE::INTEGER, ATTR_TYPE::getCase(ATTR_TYPE::INTEGER->value()));
        $this->assertEquals(ATTR_TYPE::DATETIME, ATTR_TYPE::getCase(ATTR_TYPE::DATETIME->value()));
        $this->assertEquals(ATTR_TYPE::DECIMAL, ATTR_TYPE::getCase(ATTR_TYPE::DECIMAL->value()));
        $this->assertEquals(ATTR_TYPE::STRING, ATTR_TYPE::getCase(ATTR_TYPE::STRING->value()));
        $this->assertEquals(ATTR_TYPE::TEXT, ATTR_TYPE::getCase(ATTR_TYPE::TEXT->value()));
    }
}
