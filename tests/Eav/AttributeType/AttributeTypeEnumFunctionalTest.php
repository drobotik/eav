<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeType;

use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\AttributeException;
use PHPUnit\Framework\TestCase;

class AttributeTypeEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\ATTR_TYPE::value
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
     * @covers \Drobotik\Eav\Enum\ATTR_TYPE::isValid
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
     * @covers \Drobotik\Eav\Enum\ATTR_TYPE::valueTable
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
     * @covers \Drobotik\Eav\Enum\ATTR_TYPE::doctrineType
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
     * @covers \Drobotik\Eav\Enum\ATTR_TYPE::migrateOptions
     */
    public function migrate_options() {
        $this->assertEquals([], ATTR_TYPE::INTEGER->migrateOptions());
        $this->assertEquals([], ATTR_TYPE::DATETIME->migrateOptions());
        $this->assertEquals([
            'precision' => 21,
            'scale' => 6
        ], ATTR_TYPE::DECIMAL->migrateOptions());
        $this->assertEquals([], ATTR_TYPE::STRING->migrateOptions());
        $this->assertEquals([], ATTR_TYPE::TEXT->migrateOptions());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\ATTR_TYPE::validationRule
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
     * @covers \Drobotik\Eav\Enum\ATTR_TYPE::getCase
     */
    public function get_case() {
        $this->assertEquals(ATTR_TYPE::INTEGER, ATTR_TYPE::getCase(ATTR_TYPE::INTEGER->value()));
        $this->assertEquals(ATTR_TYPE::DATETIME, ATTR_TYPE::getCase(ATTR_TYPE::DATETIME->value()));
        $this->assertEquals(ATTR_TYPE::DECIMAL, ATTR_TYPE::getCase(ATTR_TYPE::DECIMAL->value()));
        $this->assertEquals(ATTR_TYPE::STRING, ATTR_TYPE::getCase(ATTR_TYPE::STRING->value()));
        $this->assertEquals(ATTR_TYPE::TEXT, ATTR_TYPE::getCase(ATTR_TYPE::TEXT->value()));
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\ATTR_TYPE::getCase
     */
    public function get_case_exception() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNSUPPORTED_TYPE, 'test'));
        ATTR_TYPE::getCase("test");
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\ATTR_TYPE::randomValue
     */
    public function random_value() {
        $this->assertTrue(is_string(ATTR_TYPE::STRING->randomValue()));
        $this->assertTrue(is_int(ATTR_TYPE::INTEGER->randomValue()));
        $this->assertTrue(is_float(ATTR_TYPE::DECIMAL->randomValue()));
        $this->assertTrue(Carbon::createFromFormat('Y-m-d H:i:s', ATTR_TYPE::DATETIME->randomValue()) !== false);
        $this->assertTrue(is_string(ATTR_TYPE::TEXT->randomValue()));
    }
}
