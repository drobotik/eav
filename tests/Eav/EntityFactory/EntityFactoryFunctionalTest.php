<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityFactory;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Exception\EntityFactoryException;
use Drobotik\Eav\Factory\EntityFactory;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueDatetimeModel;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueIntegerModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Model\ValueTextModel;
use Drobotik\Eav\Result\EntityFactoryResult;
use PDO;
use Tests\TestCase;

class EntityFactoryFunctionalTest extends TestCase
{
    private EntityFactory $factory;


    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new EntityFactory();
    }

    protected function getFactoryDefaultConfig(): array
    {
        return [
            ATTR_TYPE::STRING->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::STRING->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::STRING->randomValue(),
                ]
            ],
            ATTR_TYPE::INTEGER->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::INTEGER->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::INTEGER->randomValue(),
                ]
            ],
            ATTR_TYPE::DECIMAL->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::DECIMAL->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::DECIMAL->randomValue(),
                ]
            ],
            ATTR_TYPE::DATETIME->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::DATETIME->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::DATETIME->randomValue(),
                ]
            ],
            ATTR_TYPE::TEXT->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::TEXT->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::TEXT->randomValue(),
                ]
            ]
        ];
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_entity() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $result = $this->factory->create([], $domainKey, $setKey);
        $this->assertInstanceOf(EntityFactoryResult::class, $result);

        $conn = Connection::get()->getNativeConnection();
        $sql = sprintf(
            "SELECT * FROM %s WHERE %s = :setKey AND %s = :domainKey",
            _ENTITY::table(),
            _ENTITY::DOMAIN_ID->column(),
            _ENTITY::ATTR_SET_ID->column()
        );
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':setKey', $setKey);
        $stmt->bindValue(':domainKey', $domainKey);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertEquals(1, count($result));

        $this->assertEquals([
            _ENTITY::ID->column() => 1,
            _ENTITY::DOMAIN_ID->column() => $domainKey,
            _ENTITY::ATTR_SET_ID->column() => $setKey,
            _ENTITY::SERVICE_KEY->column() => null
        ], $result[0]);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_attributes_no_group_exception() {
        $this->expectException(EntityFactoryException::class);
        $this->expectExceptionMessage("Group key must be provided!");
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $config = $this->getFactoryDefaultConfig();
        $this->factory->create($config, $domainKey, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_attributes_not_existing_group_exception() {
        $this->expectException(EntityFactoryException::class);
        $this->expectExceptionMessage('This group is not belongs to attribute set');
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $config = $this->getFactoryDefaultConfig();
        $config[ATTR_TYPE::STRING->value()][ATTR_FACTORY::GROUP->field()] = 1;
        $config[ATTR_TYPE::INTEGER->value()][ATTR_FACTORY::GROUP->field()] = 2;
        $config[ATTR_TYPE::DECIMAL->value()][ATTR_FACTORY::GROUP->field()] = 3;
        $config[ATTR_TYPE::DATETIME->value()][ATTR_FACTORY::GROUP->field()] = 4;
        $config[ATTR_TYPE::TEXT->value()][ATTR_FACTORY::GROUP->field()] = 5;

        $this->factory->create($config, $domainKey, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_attributes() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $config[ATTR_TYPE::STRING->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;
        $config[ATTR_TYPE::INTEGER->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;
        $config[ATTR_TYPE::DECIMAL->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;
        $config[ATTR_TYPE::DATETIME->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;
        $config[ATTR_TYPE::TEXT->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;

        $result = $this->factory->create($config, $domainKey, $setKey);

        // check attributes created
        /** @var AttributeModel $string */
        /** @var AttributeModel $integer */
        /** @var AttributeModel $decimal */
        /** @var AttributeModel $datetime */
        /** @var AttributeModel $text */
        $string = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::STRING->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::STRING->value())->first();
        $integer = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::INTEGER->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::INTEGER->value())->first();
        $decimal = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DECIMAL->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::DECIMAL->value())->first();
        $datetime = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DATETIME->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::DATETIME->value())->first();
        $text = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::TEXT->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::TEXT->value())->first();

        $this->assertNotNull($string);
        $this->assertNotNull($integer);
        $this->assertNotNull($decimal);
        $this->assertNotNull($datetime);
        $this->assertNotNull($text);

        $this->assertEquals($config[ATTR_TYPE::STRING->value()][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $string->getDefaultValue());
        $this->assertEquals($config[ATTR_TYPE::INTEGER->value()][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $integer->getDefaultValue());
        $this->assertEquals($config[ATTR_TYPE::DECIMAL->value()][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $decimal->getDefaultValue());
        $this->assertEquals($config[ATTR_TYPE::DATETIME->value()][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $datetime->getDefaultValue());
        $this->assertEquals($config[ATTR_TYPE::TEXT->value()][ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::DEFAULT_VALUE->column()], $text->getDefaultValue());

        $attributes = $result->getAttributes();
        $this->assertCount(5, $attributes);
        $this->assertEquals($string->toArray(), $attributes[ATTR_TYPE::STRING->value()]->toArray());
        $this->assertEquals($integer->toArray(), $attributes[ATTR_TYPE::INTEGER->value()]->toArray());
        $this->assertEquals($decimal->toArray(), $attributes[ATTR_TYPE::DECIMAL->value()]->toArray());
        $this->assertEquals($datetime->toArray(), $attributes[ATTR_TYPE::DATETIME->value()]->toArray());
        $this->assertEquals($text->toArray(), $attributes[ATTR_TYPE::TEXT->value()]->toArray());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_attribute_array_not_provided() {
        $this->expectException(EntityFactoryException::class);
        $this->expectExceptionMessage(EntityFactoryException::UNDEFINED_ATTRIBUTE_ARRAY);
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $field = $config[ATTR_TYPE::STRING->value()];
        $field[ATTR_FACTORY::GROUP->field()] = $groupKey;
        unset($field[ATTR_FACTORY::ATTRIBUTE->field()]);

        $this->factory->create([$field], $domainKey, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_attribute_name_not_provided() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNDEFINED_NAME));
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $field = $config[ATTR_TYPE::STRING->value()];
        $field[ATTR_FACTORY::GROUP->field()] = $groupKey;
        unset($field[ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::NAME->column()]);

        $this->factory->create([$field], $domainKey, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_attribute_type_not_provided() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNDEFINED_TYPE));
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $field = $config[ATTR_TYPE::STRING->value()];
        $field[ATTR_FACTORY::GROUP->field()] = $groupKey;
        unset($field[ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::TYPE->column()]);

        $this->factory->create([$field], $domainKey, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_attribute_type_not_supported() {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNSUPPORTED_TYPE, 'test'));
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $field = $config[ATTR_TYPE::STRING->value()];
        $field[ATTR_FACTORY::GROUP->field()] = $groupKey;
        $field[ATTR_FACTORY::ATTRIBUTE->field()][_ATTR::TYPE->column()] = "test";

        $this->factory->create([$field], $domainKey, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_pivot_table_rows() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupOneKey = $this->eavFactory->createGroup($setKey);
        $groupTwoKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $config[ATTR_TYPE::STRING->value()][ATTR_FACTORY::GROUP->field()] = $groupOneKey;
        $config[ATTR_TYPE::INTEGER->value()][ATTR_FACTORY::GROUP->field()] = $groupOneKey;
        $config[ATTR_TYPE::DECIMAL->value()][ATTR_FACTORY::GROUP->field()] = $groupOneKey;
        $config[ATTR_TYPE::DATETIME->value()][ATTR_FACTORY::GROUP->field()] = $groupTwoKey;
        $config[ATTR_TYPE::TEXT->value()][ATTR_FACTORY::GROUP->field()] = $groupTwoKey;

        $result = $this->factory->create($config, $domainKey, $setKey);

        /** @var AttributeModel $string */
        /** @var AttributeModel $integer */
        /** @var AttributeModel $decimal */
        /** @var AttributeModel $datetime */
        /** @var AttributeModel $text */
        $string = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::STRING->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::STRING->value())->firstOrFail();
        $integer = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::INTEGER->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::INTEGER->value())->firstOrFail();
        $decimal = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DECIMAL->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::DECIMAL->value())->firstOrFail();
        $datetime = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DATETIME->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::DATETIME->value())->firstOrFail();
        $text = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::TEXT->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::TEXT->value())->firstOrFail();

        /** @var PivotModel $stringPivot */
        /** @var PivotModel $integerPivot */
        /** @var PivotModel $decimalPivot */
        /** @var PivotModel $datetimePivot */
        /** @var PivotModel $textPivot */
        $stringPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domainKey)
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupOneKey)
            ->where(_PIVOT::ATTR_ID->column(), $string->getKey())->first();
        $integerPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domainKey)
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupOneKey)
            ->where(_PIVOT::ATTR_ID->column(), $integer->getKey())->first();
        $decimalPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domainKey)
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupOneKey)
            ->where(_PIVOT::ATTR_ID->column(), $decimal->getKey())->first();
        $datetimePivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domainKey)
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupTwoKey)
            ->where(_PIVOT::ATTR_ID->column(), $datetime->getKey())->first();
        $textPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domainKey)
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupTwoKey)
            ->where(_PIVOT::ATTR_ID->column(), $text->getKey())->first();

        $this->assertNotNull($stringPivot);
        $this->assertNotNull($integerPivot);
        $this->assertNotNull($decimalPivot);
        $this->assertNotNull($datetimePivot);
        $this->assertNotNull($textPivot);

        $pivots = $result->getPivots();
        $this->assertCount(5, $pivots);
        $this->assertEquals($stringPivot->toArray(), $pivots[ATTR_TYPE::STRING->value()]->toArray());
        $this->assertEquals($integerPivot->toArray(), $pivots[ATTR_TYPE::INTEGER->value()]->toArray());
        $this->assertEquals($decimalPivot->toArray(), $pivots[ATTR_TYPE::DECIMAL->value()]->toArray());
        $this->assertEquals($datetimePivot->toArray(), $pivots[ATTR_TYPE::DATETIME->value()]->toArray());
        $this->assertEquals($textPivot->toArray(), $pivots[ATTR_TYPE::TEXT->value()]->toArray());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_values() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupOneKey = $this->eavFactory->createGroup($setKey);
        $groupTwoKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $config[ATTR_TYPE::STRING->value()][ATTR_FACTORY::GROUP->field()] = $groupOneKey;
        $config[ATTR_TYPE::INTEGER->value()][ATTR_FACTORY::GROUP->field()] = $groupOneKey;
        $config[ATTR_TYPE::DECIMAL->value()][ATTR_FACTORY::GROUP->field()] = $groupOneKey;
        $config[ATTR_TYPE::DATETIME->value()][ATTR_FACTORY::GROUP->field()] = $groupTwoKey;
        $config[ATTR_TYPE::TEXT->value()][ATTR_FACTORY::GROUP->field()] = $groupTwoKey;

        $stringValue = ATTR_TYPE::STRING->randomValue();
        $integerValue = ATTR_TYPE::INTEGER->randomValue();
        $decimalValue = ATTR_TYPE::DECIMAL->randomValue();
        $datetimeValue = ATTR_TYPE::DATETIME->randomValue();
        $textValue = ATTR_TYPE::TEXT->randomValue();

        $config[ATTR_TYPE::STRING->value()][ATTR_FACTORY::VALUE->field()] = $stringValue;
        $config[ATTR_TYPE::INTEGER->value()][ATTR_FACTORY::VALUE->field()] = $integerValue;
        $config[ATTR_TYPE::DECIMAL->value()][ATTR_FACTORY::VALUE->field()] = $decimalValue;
        $config[ATTR_TYPE::DATETIME->value()][ATTR_FACTORY::VALUE->field()] = $datetimeValue;
        $config[ATTR_TYPE::TEXT->value()][ATTR_FACTORY::VALUE->field()] = $textValue;

        $result = $this->factory->create($config, $domainKey, $setKey);
        $entityKey = $result->getEntityKey();
        $attributes = $result->getAttributes();

        // check values created
        /** @var ValueStringModel $string */
        /** @var ValueIntegerModel $integer */
        /** @var ValueDecimalModel $decimal */
        /** @var ValueDatetimeModel $datetime */
        /** @var ValueTextModel $text */
        $string = ValueStringModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::STRING->value()]->getKey())
            ->first();
        $integer = ValueIntegerModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::INTEGER->value()]->getKey())
            ->first();
        $decimal = ValueDecimalModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::DECIMAL->value()]->getKey())
            ->first();
        $datetime = ValueDatetimeModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::DATETIME->value()]->getKey())
            ->first();
        $text = ValueTextModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::TEXT->value()]->getKey())
            ->first();

        $this->assertNotNull($string);
        $this->assertNotNull($integer);
        $this->assertNotNull($decimal);
        $this->assertNotNull($datetime);
        $this->assertNotNull($text);

        $this->assertEquals($stringValue, $string->getValue());
        $this->assertEquals($integerValue, $integer->getValue());
        $this->assertEquals($decimalValue, $decimal->getValue());
        $this->assertEquals($datetimeValue, $datetime->getValue());
        $this->assertEquals($textValue, $text->getValue());

        $values = $result->getValues();
        $this->assertCount(5, $values);
        $this->assertEquals($string->toArray(), $values[ATTR_TYPE::STRING->value()]->toArray());
        $this->assertEquals($integer->toArray(), $values[ATTR_TYPE::INTEGER->value()]->toArray());
        $this->assertEquals($decimal->toArray(), $values[ATTR_TYPE::DECIMAL->value()]->toArray());
        $this->assertEquals($datetime->toArray(), $values[ATTR_TYPE::DATETIME->value()]->toArray());
        $this->assertEquals($text->toArray(), $values[ATTR_TYPE::TEXT->value()]->toArray());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     */
    public function create_skip_creating_values() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $config = $this->getFactoryDefaultConfig();
        $config[ATTR_TYPE::STRING->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;
        $config[ATTR_TYPE::INTEGER->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;
        $config[ATTR_TYPE::DECIMAL->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;
        $config[ATTR_TYPE::DATETIME->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;
        $config[ATTR_TYPE::TEXT->value()][ATTR_FACTORY::GROUP->field()] = $groupKey;

        $result = $this->factory->create($config, $domainKey, $setKey);
        $entityKey = $result->getEntityKey();
        $attributes = $result->getAttributes();

        // check values created
        /** @var ValueStringModel $string */
        /** @var ValueIntegerModel $integer */
        /** @var ValueDecimalModel $decimal */
        /** @var ValueDatetimeModel $datetime */
        /** @var ValueTextModel $text */
        $string = ValueStringModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::STRING->value()]->getKey())
            ->first();
        $integer = ValueIntegerModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::INTEGER->value()]->getKey())
            ->first();
        $decimal = ValueDecimalModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::DECIMAL->value()]->getKey())
            ->first();
        $datetime = ValueDatetimeModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::DATETIME->value()]->getKey())
            ->first();
        $text = ValueTextModel::where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributes[ATTR_TYPE::TEXT->value()]->getKey())
            ->first();

        $this->assertNull($string);
        $this->assertNull($integer);
        $this->assertNull($decimal);
        $this->assertNull($datetime);
        $this->assertNull($text);

        $this->assertEquals([], $result->getValues());
    }
}
