<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeBag;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Exception\AttributeException;
use Kuperwood\Eav\Model\ValueDecimalModel;
use Kuperwood\Eav\Model\ValueStringModel;

use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{

    private Attribute $attribute;

    public function setUp(): void
    {
        parent::setUp();
        $this->attribute = new Attribute();
    }

    /** @test */
    public function bag() {
        $this->assertInstanceOf(AttributeBag::class, $this->attribute->getBag());
        $bag = new AttributeBag();
        $this->attribute->setBag($bag);
        $this->assertSame($bag, $this->attribute->getBag());
    }

    /** @test */
    public function key() {
        $this->attribute->setKey(123);
        $this->assertEquals(123, $this->attribute->getKey());
        $this->attribute->setKey(null);
        $this->assertNull($this->attribute->getKey());
    }

    /** @test */
    public function domain_key() {
        $this->attribute->setDomainKey(123);
        $this->assertEquals(123, $this->attribute->getDomainKey());
        $this->attribute->setDomainKey(null);
        $this->assertNull($this->attribute->getDomainKey());
    }

    /** @test */
    public function name() {
        $this->attribute->setName('test');
        $this->assertEquals('test', $this->attribute->getName());
        $this->attribute->setName(null);
        $this->assertNull($this->attribute->getName());
    }

    /** @test */
    public function get_type() {
        $this->attribute->setType(ATTR_TYPE::STRING->value());
        $this->assertEquals(ATTR_TYPE::STRING, $this->attribute->getType());
    }

    /** @test */
    public function get_type_throws_unexpected_type() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNEXPECTED_TYPE, 'test'));
        $this->attribute->setType("test");
        $this->attribute->getType();
    }

    /** @test */
    public function strategy() {
        $this->attribute->setStrategy('test');
        $this->assertEquals('test', $this->attribute->getStrategy());
    }

    /** @test */
    public function source() {
        $this->attribute->setSource('test');
        $this->assertEquals('test', $this->attribute->getSource());
    }

    /** @test */
    public function default_value() {
        $this->attribute->setDefaultValue('test');
        $this->assertEquals('test', $this->attribute->getDefaultValue());
    }

    /** @test */
    public function get_description() {
        $this->attribute->setDescription('test');
        $this->assertEquals('test', $this->attribute->getDescription());
    }

    /** @test */
    public function get_value_model() {
        $this->assertInstanceOf(ValueStringModel::class, $this->attribute->getValueModel());
        $this->attribute->setType(ATTR_TYPE::DECIMAL->value());
        $this->assertInstanceOf(ValueDecimalModel::class, $this->attribute->getValueModel());
    }
}