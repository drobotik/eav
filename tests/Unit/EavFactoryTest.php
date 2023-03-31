<?php

namespace Tests\Unit;

use DateTime;
use Kuperwood\Dev\EavFactory;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_DOMAIN;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Enum\_GROUP;
use Kuperwood\Eav\Enum\_SET;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\AttributeGroupModel;
use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Model\DomainModel;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\PivotModel;
use Kuperwood\Eav\Model\ValueDatetimeModel;
use Kuperwood\Eav\Model\ValueDecimalModel;
use Kuperwood\Eav\Model\ValueIntegerModel;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Model\ValueTextModel;
use Tests\TestCase;

class EavFactoryTest extends TestCase
{
    /** @test  */
    public function domain_default() {
        $result = $this->eavFactory->createDomain();
        $this->assertInstanceOf(DomainModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $data = $result->toArray();
        $this->assertArrayHasKey(_DOMAIN::ID->column(), $data);
        $this->assertArrayHasKey(_DOMAIN::NAME->column(), $data);
        $this->assertNotEmpty($data[_DOMAIN::NAME->column()]);
    }

    /** @test  */
    public function domain_input_data() {
        $input = [
            _DOMAIN::NAME->column() => 'test'
        ];
        $result = $this->eavFactory->createDomain($input);
        $this->assertInstanceOf(DomainModel::class, $result);
        $input[_DOMAIN::ID->column()] = 1;
        $this->assertEquals($input, $result->toArray());
    }

    /** @test  */
    public function entity_default() {
        $result = $this->eavFactory->createEntity();
        $this->assertInstanceOf(EntityModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getAttrSetKey());
        $data = $result->toArray();
        $this->assertEquals([
            _ENTITY::ID->column() => 1,
            _ENTITY::DOMAIN_ID->column() => 1,
            _ENTITY::ATTR_SET_ID->column() => 1
        ], $data);
        // domain created
        $this->assertEquals(1, DomainModel::query()->count());
        // attribute set created
        $this->assertEquals(1, AttributeSetModel::query()->count());
    }

    /** @test */
    public function entity_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domain = $this->eavFactory->createDomain();
        $factory->createEntity($domain);
    }

    /** @test */
    public function entity_attr_set() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createAttributeSet'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createAttributeSet');
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain);
        $factory->createEntity($domain, $attrSet);
    }

    /** @test */
    public function attribute_set() {
        $result = $this->eavFactory->createAttributeSet();
        $this->assertInstanceOf(AttributeSetModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $data = $result->toArray();
        $this->assertArrayHasKey(_SET::NAME->column(), $data);
    }

    /** @test */
    public function attribute_set_input() {
        $input = [
            _SET::NAME->column() => 'test'
        ];
        $result = $this->eavFactory->createAttributeSet(null, $input);
        $this->assertInstanceOf(AttributeSetModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals( $input[_SET::NAME->column()], $result->getName());
    }

    /** @test */
    public function attribute_set_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domain = $this->eavFactory->createDomain();
        $factory->createAttributeSet($domain);
    }

    /** @test */
    public function attribute_group() {
        $result = $this->eavFactory->createGroup();
        $this->assertInstanceOf(AttributeGroupModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getAttrSetKey());
        $data = $result->toArray();
        $this->assertArrayHasKey(_GROUP::NAME->column(), $data);
    }

    /** @test */
    public function attribute_group_input() {
        $input = [
            _GROUP::NAME->column() => 'test'
        ];
        $result = $this->eavFactory->createGroup(null, $input);
        $this->assertInstanceOf(AttributeGroupModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getAttrSetKey());
        $this->assertEquals($input[_GROUP::NAME->column()], $result->getName());
    }

    /** @test */
    public function attribute_group_attribute_set() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createAttributeSet'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createAttributeSet');
        $set = $this->eavFactory->createAttributeSet();
        $factory->createGroup($set);
    }

    /** @test */
    public function attribute() {
        $result = $this->eavFactory->createAttribute();
        $this->assertInstanceOf(AttributeModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(_ATTR::TYPE->default(), $result->getType());
        $this->assertEquals(_ATTR::STRATEGY->default(), $result->getStrategy());
        $this->assertEquals(_ATTR::SOURCE->default(), $result->getSource());
        $this->assertEquals(_ATTR::DEFAULT_VALUE->default(), $result->getDefaultValue());
        $this->assertEquals(_ATTR::DESCRIPTION->default(), $result->getDescription());
        $data = $result->toArray();
        $this->assertArrayHasKey(_ATTR::NAME->column(), $data);
    }

    /** @test */
    public function attribute_input() {
        $input = [
            _ATTR::NAME->column() => 'test',
            _ATTR::TYPE->column() => 'test',
            _ATTR::STRATEGY->column() => 'test',
            _ATTR::SOURCE->column() => 'test',
            _ATTR::DEFAULT_VALUE->column() => 'test',
            _ATTR::DESCRIPTION->column() => 'test',
        ];
        $result = $this->eavFactory->createAttribute(null, $input);
        $this->assertInstanceOf(AttributeModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals($input[_ATTR::NAME->column()], $result->getType());
        $this->assertEquals($input[_ATTR::TYPE->column()], $result->getType());
        $this->assertEquals($input[_ATTR::STRATEGY->column()], $result->getStrategy());
        $this->assertEquals($input[_ATTR::SOURCE->column()], $result->getSource());
        $this->assertEquals($input[_ATTR::DEFAULT_VALUE->column()], $result->getDefaultValue());
        $this->assertEquals($input[_ATTR::DESCRIPTION->column()], $result->getDescription());
    }

    /** @test */
    public function attribute_domain() {
        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createDomain'])
            ->getMock();
        $factory->expects($this->never())
            ->method('createDomain');
        $domain = $this->eavFactory->createDomain();
        $factory->createAttribute($domain);
    }

    /** @test */
    public function pivot() {
        $this->eavFactory->createDomain();
        $domain = $this->eavFactory->createDomain();
        $this->eavFactory->createAttributeSet($domain);
        $attrSet = $this->eavFactory->createAttributeSet($domain);
        $this->eavFactory->createGroup($attrSet);
        $group = $this->eavFactory->createGroup($attrSet);
        $this->eavFactory->createAttribute($domain);
        $attribute = $this->eavFactory->createAttribute($domain);

        $result = $this->eavFactory->createPivot($domain, $attrSet, $group, $attribute);
        $this->assertInstanceOf(PivotModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(2, $result->getDomainKey());
        $this->assertEquals(2, $result->getAttrKey());
        $this->assertEquals(2, $result->getGroupKey());
    }

    /** @test */
    public function value_string() {
        $domain = $this->eavFactory->createDomain();
        $entity = $this->eavFactory->createEntity($domain);
        $attribute = $this->eavFactory->createAttribute($domain);
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::STRING,
            $domain,
            $entity,
            $attribute,
            'test'
        );
        $this->assertInstanceOf(ValueStringModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals('test', $result->getValue());
    }

    /** @test */
    public function value_text() {
        $domain = $this->eavFactory->createDomain();
        $entity = $this->eavFactory->createEntity($domain);
        $attribute = $this->eavFactory->createAttribute($domain);
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::TEXT,
            $domain,
            $entity,
            $attribute,
            'test'
        );
        $this->assertInstanceOf(ValueTextModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals('test', $result->getValue());
    }

    /** @test */
    public function value_integer() {
        $domain = $this->eavFactory->createDomain();
        $entity = $this->eavFactory->createEntity($domain);
        $attribute = $this->eavFactory->createAttribute($domain);
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::INTEGER,
            $domain,
            $entity,
            $attribute,
            123
        );
        $this->assertInstanceOf(ValueIntegerModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals(123, $result->getValue());
    }

    /** @test */
    public function value_decimal() {
        $domain = $this->eavFactory->createDomain();
        $entity = $this->eavFactory->createEntity($domain);
        $attribute = $this->eavFactory->createAttribute($domain);
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::DECIMAL,
            $domain,
            $entity,
            $attribute,
            123.123
        );
        $this->assertInstanceOf(ValueDecimalModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals(123.123, $result->getValue());
    }

    /** @test */
    public function value_datetime() {
        $domain = $this->eavFactory->createDomain();
        $entity = $this->eavFactory->createEntity($domain);
        $attribute = $this->eavFactory->createAttribute($domain);
        $datetime = (new DateTime())->format('Y-m-d H:i:s');
        $result = $this->eavFactory->createValue(
            ATTR_TYPE::DATETIME,
            $domain,
            $entity,
            $attribute,
            $datetime
        );
        $this->assertInstanceOf(ValueDatetimeModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $this->assertEquals(1, $result->getEntityKey());
        $this->assertEquals(1, $result->getAttrKey());
        $this->assertEquals($datetime, $result->getValue());
    }
}