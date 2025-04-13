<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeBag;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Exception\AttributeException;
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
     * @covers \Kuperwood\Eav\Attribute::getBag
     * @covers \Kuperwood\Eav\Attribute::setBag
     * @covers \Kuperwood\Eav\Attribute::__construct()
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
     * @covers \Kuperwood\Eav\Attribute::getKey
     * @covers \Kuperwood\Eav\Attribute::setKey
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
     * @covers \Kuperwood\Eav\Attribute::getDomainKey
     * @covers \Kuperwood\Eav\Attribute::setDomainKey
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
     * @covers \Kuperwood\Eav\Attribute::getGroupKey
     * @covers \Kuperwood\Eav\Attribute::setGroupKey
     */
    public function group_key() {
        $this->attribute->setGroupKey('123');
        $this->assertEquals(123, $this->attribute->getGroupKey());
        $this->attribute->setGroupKey(null);
        $this->assertNull($this->attribute->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Attribute::getName
     * @covers \Kuperwood\Eav\Attribute::setName
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
     * @covers \Kuperwood\Eav\Attribute::getType
     * @covers \Kuperwood\Eav\Attribute::setType
     */
    public function get_type() {
        $this->attribute->setType(ATTR_TYPE::STRING);
        $this->assertEquals(ATTR_TYPE::STRING, $this->attribute->getType());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Attribute::getType
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
     * @covers \Kuperwood\Eav\Attribute::setStrategy
     * @covers \Kuperwood\Eav\Attribute::getStrategy
     */
    public function strategy() {
        $this->attribute->setStrategy('test');
        $this->assertEquals('test', $this->attribute->getStrategy());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Attribute::setSource
     * @covers \Kuperwood\Eav\Attribute::getSource
     */
    public function source() {
        $this->attribute->setSource('test');
        $this->assertEquals('test', $this->attribute->getSource());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Attribute::setDefaultValue
     * @covers \Kuperwood\Eav\Attribute::getDefaultValue
     */
    public function default_value() {
        $this->attribute->setDefaultValue('test');
        $this->assertEquals('test', $this->attribute->getDefaultValue());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Attribute::setDescription
     * @covers \Kuperwood\Eav\Attribute::getDescription
     */
    public function get_description() {
        $this->attribute->setDescription('test');
        $this->assertEquals('test', $this->attribute->getDescription());
    }
}