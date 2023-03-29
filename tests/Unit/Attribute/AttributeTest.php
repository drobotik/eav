<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeBag;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Exception\AttributeException;
use Kuperwood\Eav\Model\ValueDecimalModel;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Source;
use Kuperwood\Eav\Strategy;
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
    public function source() {
        $source = new Source();
        $this->attribute->setSource($source);
        $this->assertSame($source, $this->attribute->getSource());
    }

    /** @test */
    public function get_key() {
        $bag = new AttributeBag();
        $bag->setField(_ATTR::ID, 123);
        $this->attribute->setBag($bag);
        $this->assertEquals(123, $this->attribute->getKey());
    }

    /** @test */
    public function get_name() {
        $bag = new AttributeBag();
        $bag->setField(_ATTR::NAME, 'test');
        $this->attribute->setBag($bag);
        $this->assertEquals('test', $this->attribute->getName());
    }

    /** @test */
    public function get_type() {
        $bag = new AttributeBag();
        $bag->setField(_ATTR::TYPE, ATTR_TYPE::STRING);
        $this->attribute->setBag($bag);
        $this->assertEquals(ATTR_TYPE::STRING, $this->attribute->getType());
    }

    /** @test */
    public function set_type() {
        $this->attribute->setType(ATTR_TYPE::STRING->value());
        $this->assertEquals(ATTR_TYPE::STRING, $this->attribute->getType());
    }

    /** @test */
    public function set_type_throws_unexpected_type() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNEXPECTED_TYPE, 'test'));
        $this->attribute->setType('test');
    }

    /** @test */
    public function get_domain_key() {
        $bag = new AttributeBag();
        $bag->setField(_ATTR::DOMAIN_ID, 123);
        $this->attribute->setBag($bag);
        $this->assertEquals(123, $this->attribute->getDomainKey());
    }

    /** @test */
    public function get_default_value() {
        $bag = new AttributeBag();
        $bag->setField(_ATTR::DEFAULT_VALUE, 'test');
        $this->attribute->setBag($bag);
        $this->assertEquals('test', $this->attribute->getDefaultValue());
    }

    /** @test */
    public function get_description() {
        $bag = new AttributeBag();
        $bag->setField(_ATTR::DESCRIPTION, 'test');
        $this->attribute->setBag($bag);
        $this->assertEquals('test', $this->attribute->getDescription());
    }

    /** @test */
    public function set_key() {
        $this->attribute->setKey(123);
        $this->assertEquals(123, $this->attribute->getKey());
    }

    /** @test */
    public function set_name() {
        $this->attribute->setName('test');
        $this->assertEquals('test', $this->attribute->getName());
    }

    /** @test */
    public function get_value_model() {
        $this->assertInstanceOf(ValueStringModel::class, $this->attribute->getValueModel());
        $this->attribute->setType(ATTR_TYPE::DECIMAL->value());
        $this->assertInstanceOf(ValueDecimalModel::class, $this->attribute->getValueModel());
    }

}