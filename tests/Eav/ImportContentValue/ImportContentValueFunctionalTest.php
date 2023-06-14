<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportContentValue;

use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Content\Value;
use PHPUnit\Framework\TestCase;

class ImportContentValueFunctionalTest extends TestCase
{
    private Value $value;
    public function setUp(): void
    {
        parent::setUp();
        $this->value = new Value();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Value::setType
     * @covers \Drobotik\Eav\Import\Content\Value::getType
     */
    public function type()
    {
        $type = ATTR_TYPE::INTEGER;
        $this->value->setType($type);
        $this->assertSame($type, $this->value->getType());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Value::setValue
     * @covers \Drobotik\Eav\Import\Content\Value::getValue
     * @covers \Drobotik\Eav\Import\Content\Value::isEmptyValue
     */
    public function value()
    {
        $this->value->setType(ATTR_TYPE::STRING);
        $this->value->setValue('test');
        $this->assertEquals('test', $this->value->getValue());
        $this->assertFalse($this->value->isEmptyValue());
        $this->value->setValue('');
        $this->assertTrue($this->value->isEmptyValue());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Value::setEntityKey
     * @covers \Drobotik\Eav\Import\Content\Value::getEntityKey
     * @covers \Drobotik\Eav\Import\Content\Value::isEntityKey
     */
    public function entity_key()
    {
        $this->assertFalse($this->value->isEntityKey());
        $this->value->setEntityKey(22);
        $this->assertTrue($this->value->isEntityKey());
        $this->assertEquals(22, $this->value->getEntityKey());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Value::setAttributeKey
     * @covers \Drobotik\Eav\Import\Content\Value::getAttributeKey
     */
    public function attribute_key()
    {
        $this->value->setAttributeName('test');
        $this->assertEquals('test', $this->value->getAttributeName());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Value::setAttributeName
     * @covers \Drobotik\Eav\Import\Content\Value::getAttributeName
     */
    public function attribute_name()
    {
        $this->value->setAttributeKey(123);
        $this->assertEquals(123, $this->value->getAttributeKey());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Value::setLineIndex
     * @covers \Drobotik\Eav\Import\Content\Value::getLineIndex
     * @covers \Drobotik\Eav\Import\Content\Value::isLineIndex
     */
    public function line_index()
    {
        $this->assertFalse($this->value->isLineIndex());
        $this->value->setLineIndex(22);
        $this->assertTrue($this->value->isLineIndex());
        $this->assertEquals(22, $this->value->getLineIndex());
    }
}