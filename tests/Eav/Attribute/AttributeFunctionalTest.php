<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Attribute;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeBag;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueStringModel;
use PHPUnit\Framework\TestCase;

class AttributeFunctionalTest extends TestCase
{
    private Attribute $attribute;

    public function setUp(): void
    {
        parent::setUp();
        $this->attribute = new Attribute();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::getBag
     * @covers \Drobotik\Eav\Attribute::setBag
     */
    public function bag() {
        $this->assertInstanceOf(AttributeBag::class, $this->attribute->getBag());
        $bag = new AttributeBag();
        $this->attribute->setBag($bag);
        $this->assertSame($bag, $this->attribute->getBag());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::getKey
     * @covers \Drobotik\Eav\Attribute::setKey
     */
    public function key() {
        $this->attribute->setKey(123);
        $this->assertEquals(123, $this->attribute->getKey());
        $this->attribute->setKey(null);
        $this->assertNull($this->attribute->getKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::getDomainKey
     * @covers \Drobotik\Eav\Attribute::setDomainKey
     */
    public function domain_key() {
        $this->attribute->setDomainKey(123);
        $this->assertEquals(123, $this->attribute->getDomainKey());
        $this->attribute->setDomainKey(null);
        $this->assertNull($this->attribute->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::getName
     * @covers \Drobotik\Eav\Attribute::setName
     */
    public function name_accessor() {
        $this->attribute->setName('test');
        $this->assertEquals('test', $this->attribute->getName());
        $this->attribute->setName(null);
        $this->assertNull($this->attribute->getName());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::getType
     * @covers \Drobotik\Eav\Attribute::setType
     */
    public function get_type() {
        $this->attribute->setType(ATTR_TYPE::STRING->value());
        $this->assertEquals(ATTR_TYPE::STRING, $this->attribute->getType());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::getType
     */
    public function get_type_throws_unexpected_type() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNSUPPORTED_TYPE, 'test'));
        $this->attribute->setType("test");
        $this->attribute->getType();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::setStrategy
     * @covers \Drobotik\Eav\Attribute::getStrategy
     */
    public function strategy() {
        $this->attribute->setStrategy('test');
        $this->assertEquals('test', $this->attribute->getStrategy());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::setSource
     * @covers \Drobotik\Eav\Attribute::getSource
     */
    public function source() {
        $this->attribute->setSource('test');
        $this->assertEquals('test', $this->attribute->getSource());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::setDefaultValue
     * @covers \Drobotik\Eav\Attribute::getDefaultValue
     */
    public function default_value() {
        $this->attribute->setDefaultValue('test');
        $this->assertEquals('test', $this->attribute->getDefaultValue());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Attribute::setDescription
     * @covers \Drobotik\Eav\Attribute::getDescription
     */
    public function get_description() {
        $this->attribute->setDescription('test');
        $this->assertEquals('test', $this->attribute->getDescription());
    }
}