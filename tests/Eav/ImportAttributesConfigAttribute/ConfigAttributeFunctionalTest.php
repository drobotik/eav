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
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use PHPUnit\Framework\TestCase;

class ConfigAttributeFunctionalTest extends TestCase
{
    private ConfigAttribute $attribute;

    public function setUp(): void
    {
        parent::setUp();
        $this->attribute = new ConfigAttribute();
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
        $this->attribute->setFields([123]);
        $this->assertEquals([123], $this->attribute->getFields());
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
        $this->attribute->setFields([_ATTR::NAME->column() => 'test']);
        $this->assertEquals('test', $this->attribute->getName());
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
        $this->attribute->setFields([_ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()]);
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
        $this->attribute->setFields([_ATTR::ID->column() => 123]);
        $this->assertEquals( 123, $this->attribute->getKey());
    }
}