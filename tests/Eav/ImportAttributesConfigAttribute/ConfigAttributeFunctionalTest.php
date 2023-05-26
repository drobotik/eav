<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesConfigAttribute;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use PHPUnit\Framework\TestCase;

class ConfigAttributeFunctionalTest extends TestCase
{
    private ConfigAttribute $attribute;
    private array $fields;

    public function setUp(): void
    {
        parent::setUp();
        $this->attribute = new ConfigAttribute();
        $this->fields = [
            _ATTR::ID->column() => 123,
            _ATTR::NAME->column() => 'name',
            _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
        ];
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigAttribute::setFields
     * @covers \Drobotik\Eav\Import\Attributes\ConfigAttribute::getFields
     */
    public function fields()
    {
        $this->attribute->setFields($this->fields);
        $this->assertEquals($this->fields, $this->attribute->getFields());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigAttribute::getGroupKey
     * @covers \Drobotik\Eav\Import\Attributes\ConfigAttribute::setGroupKey
     */
    public function group_key()
    {
        $this->attribute->setGroupKey(123);
        $this->assertEquals(123, $this->attribute->getGroupKey());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigAttribute::getName
     */
    public function name()
    {
        $this->attribute->setFields($this->fields);
        $this->assertEquals('name', $this->attribute->getName());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigAttribute::getType
     */
    public function type()
    {
        $this->attribute->setFields($this->fields);
        $this->assertEquals( ATTR_TYPE::TEXT, $this->attribute->getType());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigAttribute::getKey
     */
    public function key()
    {
        $this->attribute->setFields($this->fields);
        $this->assertEquals( 123, $this->attribute->getKey());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigAttribute::validate
     */
    public function validate_name()
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(AttributeException::UNDEFINED_NAME);
        $this->attribute->setFields([]);
        $this->attribute->validate();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigAttribute::validate
     */
    public function validate_type()
    {
        $this->attribute->setFields([_ATTR::NAME->column() => 'test']);
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(AttributeException::UNDEFINED_TYPE);
        $this->attribute->validate();
    }
}