<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EavFactory;

use Carbon\Carbon;
use DateTime;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Enum\_SET;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Factory\EavFactory;
use Drobotik\Eav\Model\AttributeGroupModel;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueDatetimeModel;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueIntegerModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Model\ValueTextModel;
use Drobotik\Eav\Result\EntityFactoryResult;
use Drobotik\Eav\Result\Result;
use Tests\TestCase;

class EavFactoryFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers EavFactory::createDomain
     */
    public function domain_default() {
        $result = $this->eavFactory->createDomain();
        $this->assertInstanceOf(DomainModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $data = $result->toArray();
        $this->assertArrayHasKey(_DOMAIN::ID->column(), $data);
        $this->assertArrayHasKey(_DOMAIN::NAME->column(), $data);
        $this->assertNotEmpty($data[_DOMAIN::NAME->column()]);
    }
    /**
     * @test
     * @group functional
     * @covers EavFactory::createDomain
     */
    public function domain_input_data() {
        $input = [
            _DOMAIN::NAME->column() => 'test'
        ];
        $result = $this->eavFactory->createDomain($input);
        $this->assertInstanceOf(DomainModel::class, $result);
        $input[_DOMAIN::ID->column()] = 1;
        $this->assertEquals($input, $result->toArray());
    }
    /**
     * @test
     * @group functional
     * @covers EavFactory::createEntity
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createAttributeSet
     */
    public function attribute_set() {
        $result = $this->eavFactory->createAttributeSet();
        $this->assertInstanceOf(AttributeSetModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getDomainKey());
        $data = $result->toArray();
        $this->assertArrayHasKey(_SET::NAME->column(), $data);
    }
    /**
     * @test
     * @group functional
     * @covers EavFactory::createAttributeSet
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createGroup
     */
    public function attribute_group() {
        $result = $this->eavFactory->createGroup();
        $this->assertInstanceOf(AttributeGroupModel::class, $result);
        $this->assertEquals(1, $result->getKey());
        $this->assertEquals(1, $result->getAttrSetKey());
        $data = $result->toArray();
        $this->assertArrayHasKey(_GROUP::NAME->column(), $data);
    }
    /**
     * @test
     * @group functional
     * @covers EavFactory::createGroup
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createAttribute
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createAttribute
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createPivot
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createValue
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createValue
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createValue
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createValue
     */
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
    /**
     * @test
     * @group functional
     * @covers EavFactory::createValue
     */
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

    /**
     * @test
     * @group functional
     * @covers EavFactory::createEavEntity
     */
    public function create_eav_entity() {
        $domain = $this->eavFactory->createDomain();
        $set = $this->eavFactory->createAttributeSet($domain);
        $groupOne = $this->eavFactory->createGroup($set);
        $groupTwo = $this->eavFactory->createGroup($set);
        $nameString = "string";
        $nameInteger = "integer";
        $nameDecimal = "decimal";
        $nameDatetime = "datetime";
        $nameText = "text";
        $fields = [
            $nameString => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => $nameString,
                    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value(),
                    _ATTR::DEFAULT_VALUE->column() => 'string default'
                ],
                ATTR_FACTORY::GROUP->field() => $groupOne->getKey(),
                ATTR_FACTORY::VALUE->field() => $this->faker->word
            ],
            $nameInteger => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => $nameInteger,
                    _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
                    _ATTR::DEFAULT_VALUE->column() => 'integer default'
                ],
                ATTR_FACTORY::GROUP->field() => $groupOne->getKey(),
                ATTR_FACTORY::VALUE->field() => $this->faker->randomNumber()
            ],
            $nameDecimal => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => $nameDecimal,
                    _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value(),
                    _ATTR::DEFAULT_VALUE->column() => 'decimal default'
                ],
                ATTR_FACTORY::GROUP->field() => $groupOne->getKey(),
                ATTR_FACTORY::VALUE->field() => $this->faker->randomFloat()
            ],
            $nameDatetime => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => $nameDatetime,
                    _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value(),
                    _ATTR::DEFAULT_VALUE->column() => 'datetime default'
                ],
                ATTR_FACTORY::GROUP->field() => $groupTwo->getKey(),
                ATTR_FACTORY::VALUE->field() => Carbon::now()->format('Y-m-d H:i:s')
            ],
            $nameText => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => $nameText,
                    _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value(),
                    _ATTR::DEFAULT_VALUE->column() => 'type default'
                ],
                ATTR_FACTORY::GROUP->field() => $groupTwo->getKey(),
                ATTR_FACTORY::VALUE->field() => $this->faker->text
            ],
        ];

        $this->eavFactory->createGroup($set);
        $result = $this->eavFactory->createEavEntity($fields, $domain, $set);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
        // check result is entity result
        $factoryResult = $result->getData();
        $this->assertInstanceOf(EntityFactoryResult::class, $factoryResult);

        // check entity record created
        /** @var EntityModel $entityModel */
        $entityModel = EntityModel
            ::where(_ENTITY::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ENTITY::ATTR_SET_ID->column(), $set->getKey())
            ->first();
        $this->assertNotNull($entityModel);
        $this->assertEquals($entityModel->toArray(), $factoryResult->getEntityModel()->toArray());

        // check attributes created
        /** @var AttributeModel $stringAttribute */
        /** @var AttributeModel $integerAttribute */
        /** @var AttributeModel $decimalAttribute */
        /** @var AttributeModel $datetimeAttribute */
        /** @var AttributeModel $textAttribute */

        $stringAttribute = AttributeModel
            ::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::STRING->value())
            ->where(_ATTR::NAME->column(), $nameString)->first();
        $integerAttribute = AttributeModel
            ::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::INTEGER->value())
            ->where(_ATTR::NAME->column(), $nameInteger)->first();
        $decimalAttribute = AttributeModel
            ::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DECIMAL->value())
            ->where(_ATTR::NAME->column(), $nameDecimal)->first();
        $datetimeAttribute = AttributeModel
            ::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DATETIME->value())
            ->where(_ATTR::NAME->column(), $nameDatetime)->first();
        $textAttribute = AttributeModel
            ::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::TEXT->value())
            ->where(_ATTR::NAME->column(), $nameText)->first();

        $this->assertNotNull($stringAttribute);
        $this->assertNotNull($integerAttribute);
        $this->assertNotNull($decimalAttribute);
        $this->assertNotNull($datetimeAttribute);
        $this->assertNotNull($textAttribute);

        $this->assertEquals($fields[$nameString][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $stringAttribute->getDefaultValue());
        $this->assertEquals($fields[$nameInteger][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $integerAttribute->getDefaultValue());
        $this->assertEquals($fields[$nameDecimal][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $decimalAttribute->getDefaultValue());
        $this->assertEquals($fields[$nameDatetime][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $datetimeAttribute->getDefaultValue());
        $this->assertEquals($fields[$nameText][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $textAttribute->getDefaultValue());

        // check attributes in returned result
        $attributes = $factoryResult->getAttributes();
        $this->assertArrayHasKey($stringAttribute->getName(), $attributes);
        $this->assertArrayHasKey($integerAttribute->getName(), $attributes);
        $this->assertArrayHasKey($decimalAttribute->getName(), $attributes);
        $this->assertArrayHasKey($datetimeAttribute->getName(), $attributes);
        $this->assertArrayHasKey($textAttribute->getName(), $attributes);
        $this->assertEquals($stringAttribute->toArray(), $attributes[$stringAttribute->getName()]->toArray());
        $this->assertEquals($integerAttribute->toArray(), $attributes[$integerAttribute->getName()]->toArray());
        $this->assertEquals($decimalAttribute->toArray(), $attributes[$decimalAttribute->getName()]->toArray());
        $this->assertEquals($datetimeAttribute->toArray(), $attributes[$datetimeAttribute->getName()]->toArray());
        $this->assertEquals($textAttribute->toArray(), $attributes[$textAttribute->getName()]->toArray());

        // check attributes linked in pivot table
        /** @var PivotModel $stringPivot */
        /** @var PivotModel $integerPivot */
        /** @var PivotModel $decimalPivot */
        /** @var PivotModel $datetimePivot */
        /** @var PivotModel $textPivot */

        $stringPivot = PivotModel
            ::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $set->getKey())
            ->where(_PIVOT::GROUP_ID->column(), $groupOne->getKey()) // group one
            ->where(_PIVOT::ATTR_ID->column(), $stringAttribute->getKey())->first();
        $integerPivot = PivotModel
            ::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $set->getKey())
            ->where(_PIVOT::GROUP_ID->column(), $groupOne->getKey()) // group one
            ->where(_PIVOT::ATTR_ID->column(), $integerAttribute->getKey())->first();
        $decimalPivot = PivotModel
            ::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $set->getKey())
            ->where(_PIVOT::GROUP_ID->column(), $groupOne->getKey()) // group one
            ->where(_PIVOT::ATTR_ID->column(), $decimalAttribute->getKey())->first();
        $datetimePivot = PivotModel
            ::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $set->getKey())
            ->where(_PIVOT::GROUP_ID->column(), $groupTwo->getKey()) // group two
            ->where(_PIVOT::ATTR_ID->column(), $datetimeAttribute->getKey())->first();
        $textPivot = PivotModel
            ::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $set->getKey())
            ->where(_PIVOT::GROUP_ID->column(), $groupTwo->getKey()) // group two
            ->where(_PIVOT::ATTR_ID->column(), $textAttribute->getKey())->first();

        $this->assertNotNull($stringPivot);
        $this->assertNotNull($integerPivot);
        $this->assertNotNull($decimalPivot);
        $this->assertNotNull($datetimePivot);
        $this->assertNotNull($textPivot);

        // check values created
        /** @var ValueStringModel $stringValue */
        /** @var ValueIntegerModel $integerValue */
        /** @var ValueDecimalModel $decimalValue */
        /** @var ValueDatetimeModel $datetimeValue */
        /** @var ValueTextModel $textValue */

        $stringValue = ValueStringModel
            ::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
            ->where(_VALUE::ENTITY_ID->column(), $entityModel->getKey())
            ->where(_VALUE::ATTRIBUTE_ID->column(), $stringAttribute->getKey())
            ->first();
        $integerValue = ValueIntegerModel
            ::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
            ->where(_VALUE::ENTITY_ID->column(), $entityModel->getKey())
            ->where(_VALUE::ATTRIBUTE_ID->column(), $integerAttribute->getKey())
            ->first();
        $decimalValue = ValueDecimalModel
            ::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
            ->where(_VALUE::ENTITY_ID->column(), $entityModel->getKey())
            ->where(_VALUE::ATTRIBUTE_ID->column(), $decimalAttribute->getKey())
            ->first();
        $datetimeValue = ValueDatetimeModel
            ::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
            ->where(_VALUE::ENTITY_ID->column(), $entityModel->getKey())
            ->where(_VALUE::ATTRIBUTE_ID->column(), $datetimeAttribute->getKey())
            ->first();
        $textValue = ValueTextModel
            ::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
            ->where(_VALUE::ENTITY_ID->column(), $entityModel->getKey())
            ->where(_VALUE::ATTRIBUTE_ID->column(), $textAttribute->getKey())
            ->first();

        $this->assertNotNull($stringValue);
        $this->assertNotNull($integerValue);
        $this->assertNotNull($decimalValue);
        $this->assertNotNull($datetimeValue);
        $this->assertNotNull($textValue);

        $this->assertEquals($stringValue->getValue(), $fields[$nameString][ATTR_FACTORY::VALUE->field()]);
        $this->assertEquals($integerValue->getValue(), $fields[$nameInteger][ATTR_FACTORY::VALUE->field()]);
        $this->assertEquals($decimalValue->getValue(), $fields[$nameDecimal][ATTR_FACTORY::VALUE->field()]);
        $this->assertEquals($datetimeValue->getValue(), $fields[$nameDatetime][ATTR_FACTORY::VALUE->field()]);
        $this->assertEquals($textValue->getValue(), $fields[$nameText][ATTR_FACTORY::VALUE->field()]);
    }
}