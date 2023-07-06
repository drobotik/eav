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
use Drobotik\Eav\Model\ValueBase;
use Drobotik\Eav\Result\EntityFactoryResult;
use Drobotik\Eav\Value\ValueParser;
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
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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
     * @group functional
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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
     * @group functional
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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
     * @group functional
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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

        $stringConfig = $config[ATTR_TYPE::STRING->value()][ATTR_FACTORY::ATTRIBUTE->field()];
        $integerConfig = $config[ATTR_TYPE::INTEGER->value()][ATTR_FACTORY::ATTRIBUTE->field()];
        $decimalConfig = $config[ATTR_TYPE::DECIMAL->value()][ATTR_FACTORY::ATTRIBUTE->field()];
        $datetimeConfig = $config[ATTR_TYPE::DATETIME->value()][ATTR_FACTORY::ATTRIBUTE->field()];
        $textConfig = $config[ATTR_TYPE::TEXT->value()][ATTR_FACTORY::ATTRIBUTE->field()];

        $result = $this->factory->create($config, $domainKey, $setKey);

        // check attributes created
        $qb = Connection::get()->createQueryBuilder();
        $q = $qb->select('*')->from(_ATTR::table())
            ->where(sprintf('%s = :domain AND %s = :type AND %s = :name',
                _ATTR::DOMAIN_ID->column(), _ATTR::TYPE->column(), _ATTR::NAME->column()));

        $string = $q->setParameters([
                'domain' => $domainKey,
                'type' => ATTR_TYPE::STRING->value(),
                'name' => ATTR_TYPE::STRING->value()
            ])->executeQuery()->fetchAssociative();

        $integer = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::INTEGER->value(),
            'name' => ATTR_TYPE::INTEGER->value()
        ])->executeQuery()->fetchAssociative();

        $decimal =  $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DECIMAL->value(),
            'name' => ATTR_TYPE::DECIMAL->value()
        ])->executeQuery()->fetchAssociative();

        $datetime = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DATETIME->value(),
            'name' => ATTR_TYPE::DATETIME->value()
        ])->executeQuery()->fetchAssociative();

        $text = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::TEXT->value(),
            'name' => ATTR_TYPE::TEXT->value()
        ])->executeQuery()->fetchAssociative();

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        $stringKey = $string[_ATTR::ID->column()];
        $integerKey = $integer[_ATTR::ID->column()];
        $decimalKey = $decimal[_ATTR::ID->column()];
        $datetimeKey = $datetime[_ATTR::ID->column()];
        $textKey = $text[_ATTR::ID->column()];


        $expectedString = array_merge(
            _ATTR::bag(),
            $stringConfig,
            [_ATTR::ID->column() => $stringKey, _ATTR::DOMAIN_ID->column() => $domainKey]
        );
        $expectedInteger = array_merge(
            _ATTR::bag(),
            $integerConfig,
            [_ATTR::ID->column() => $integerKey, _ATTR::DOMAIN_ID->column() => $domainKey]
        );
        $expectedDecimal = array_merge(
            _ATTR::bag(),
            $decimalConfig,
            [_ATTR::ID->column() => $decimalKey, _ATTR::DOMAIN_ID->column() => $domainKey]
        );
        $expectedDatetime = array_merge(
            _ATTR::bag(),
            $datetimeConfig,
            [_ATTR::ID->column() => $datetimeKey, _ATTR::DOMAIN_ID->column() => $domainKey]
        );
        $expectedText = array_merge(
            _ATTR::bag(),
            $textConfig,
            [_ATTR::ID->column() => $textKey, _ATTR::DOMAIN_ID->column() => $domainKey]
        );

        $this->assertEquals($expectedString, $string);
        $this->assertEquals($expectedInteger, $integer);
        $this->assertEquals($expectedDecimal, $decimal);
        $this->assertEquals($expectedDatetime, $datetime);
        $this->assertEquals($expectedText, $text);

        $attributes = $result->getAttributes();
        $this->assertEquals([
            $string[_ATTR::NAME->column()] => [
                _ATTR::ID->column() => $string[_ATTR::ID->column()],
                _ATTR::NAME->column() => $string[_ATTR::NAME->column()]
            ],
            $integer[_ATTR::NAME->column()] => [
                _ATTR::ID->column() => $integer[_ATTR::ID->column()],
                _ATTR::NAME->column() => $integer[_ATTR::NAME->column()]
            ],
            $decimal[_ATTR::NAME->column()] => [
                _ATTR::ID->column() => $decimal[_ATTR::ID->column()],
                _ATTR::NAME->column() => $decimal[_ATTR::NAME->column()]
            ],
            $datetime[_ATTR::NAME->column()] => [
                _ATTR::ID->column() => $datetime[_ATTR::ID->column()],
                _ATTR::NAME->column() => $datetime[_ATTR::NAME->column()]
            ],
            $text[_ATTR::NAME->column()] => [
                _ATTR::ID->column() => $text[_ATTR::ID->column()],
                _ATTR::NAME->column() => $text[_ATTR::NAME->column()]
            ]
        ], $attributes);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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

        $qb = Connection::get()->createQueryBuilder();
        $q = $qb->select('*')->from(_ATTR::table())
            ->where(sprintf('%s = :domain AND %s = :type AND %s = :name',
                _ATTR::DOMAIN_ID->column(), _ATTR::TYPE->column(), _ATTR::NAME->column()));


        $string = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::STRING->value(),
            'name' => ATTR_TYPE::STRING->value()
        ])->executeQuery()->fetchAssociative();
        $stringKey = $string[_ATTR::ID->column()];

        $integer = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::INTEGER->value(),
            'name' => ATTR_TYPE::INTEGER->value()
        ])->executeQuery()->fetchAssociative();
        $integerKey = $integer[_ATTR::ID->column()];

        $decimal =  $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DECIMAL->value(),
            'name' => ATTR_TYPE::DECIMAL->value()
        ])->executeQuery()->fetchAssociative();
        $decimalKey = $decimal[_ATTR::ID->column()];

        $datetime = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DATETIME->value(),
            'name' => ATTR_TYPE::DATETIME->value()
        ])->executeQuery()->fetchAssociative();
        $datetimeKey = $datetime[_ATTR::ID->column()];

        $text = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::TEXT->value(),
            'name' => ATTR_TYPE::TEXT->value()
        ])->executeQuery()->fetchAssociative();
        $textKey = $text[_ATTR::ID->column()];

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        $pivotModel = new PivotModel();
        $stringPivot = $pivotModel->findOne($domainKey, $setKey, $groupOneKey, $string[_ATTR::ID->column()]);
        $integerPivot = $pivotModel->findOne($domainKey, $setKey, $groupOneKey, $integer[_ATTR::ID->column()]);
        $decimalPivot = $pivotModel->findOne($domainKey, $setKey, $groupOneKey, $decimal[_ATTR::ID->column()]);
        $datetimePivot = $pivotModel->findOne($domainKey, $setKey, $groupTwoKey, $datetime[_ATTR::ID->column()]);
        $textPivot = $pivotModel->findOne($domainKey, $setKey, $groupTwoKey, $text[_ATTR::ID->column()]);

        $this->assertIsArray($stringPivot);
        $this->assertIsArray($integerPivot);
        $this->assertIsArray($decimalPivot);
        $this->assertIsArray($datetimePivot);
        $this->assertIsArray($textPivot);

        $pivots = $result->getPivots();
        $this->assertCount(5, $pivots);

        $this->assertEquals($stringPivot[_PIVOT::ID->column()], $pivots[$stringKey]);
        $this->assertEquals($integerPivot[_PIVOT::ID->column()], $pivots[$integerKey]);
        $this->assertEquals($decimalPivot[_PIVOT::ID->column()], $pivots[$decimalKey]);
        $this->assertEquals($datetimePivot[_PIVOT::ID->column()], $pivots[$datetimeKey]);
        $this->assertEquals($textPivot[_PIVOT::ID->column()], $pivots[$textKey]);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EntityFactory::create
     * @covers \Drobotik\Eav\Factory\EntityFactory::makeNewResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::getResult
     * @covers \Drobotik\Eav\Factory\EntityFactory::validateFields
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleField
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
        $valueModel = $this->makeValueModel();

        // check values created
        $stringKey = $attributes[ATTR_TYPE::STRING->value()][_ATTR::ID->column()];
        $string = $valueModel->find(
            ATTR_TYPE::STRING->valueTable(),
            $domainKey,
            $entityKey,
            $stringKey
        );


        $integerKey = $attributes[ATTR_TYPE::INTEGER->value()][_ATTR::ID->column()];
        $integer = $valueModel->find(
            ATTR_TYPE::INTEGER->valueTable(),
            $domainKey,
            $entityKey,
            $integerKey
        );

        $decimalKey = $attributes[ATTR_TYPE::DECIMAL->value()][_ATTR::ID->column()];
        $decimal = $valueModel->find(
            ATTR_TYPE::DECIMAL->valueTable(),
            $domainKey,
            $entityKey,
            $decimalKey
        );

        $datetimeKey = $attributes[ATTR_TYPE::DATETIME->value()][_ATTR::ID->column()];
        $datetime = $valueModel->find(
            ATTR_TYPE::DATETIME->valueTable(),
            $domainKey,
            $entityKey,
            $datetimeKey
        );

        $textKey = $attributes[ATTR_TYPE::TEXT->value()][_ATTR::ID->column()];
        $text = $valueModel->find(
            ATTR_TYPE::TEXT->valueTable(),
            $domainKey,
            $entityKey,
            $textKey
        );

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        $parser = $this->makeValueParser();

        $this->assertEquals($parser->parse(ATTR_TYPE::STRING, $stringValue), $string[_VALUE::VALUE->column()]);
        $this->assertEquals($parser->parse(ATTR_TYPE::INTEGER, $integerValue), $integer[_VALUE::VALUE->column()]);
        $this->assertEquals($parser->parse(ATTR_TYPE::DECIMAL, $decimalValue), $decimal[_VALUE::VALUE->column()]);
        $this->assertEquals($parser->parse(ATTR_TYPE::DATETIME, $datetimeValue), $datetime[_VALUE::VALUE->column()]);
        $this->assertEquals($parser->parse(ATTR_TYPE::TEXT, $textValue), $text[_VALUE::VALUE->column()]);

        $values = $result->getValues();
        $this->assertCount(5, $values);
        $this->assertEquals($string[_VALUE::ID->column()], $values[$stringKey]);
        $this->assertEquals($integer[_VALUE::ID->column()], $values[$integerKey]);
        $this->assertEquals($decimal[_VALUE::ID->column()], $values[$decimalKey]);
        $this->assertEquals($datetime[_VALUE::ID->column()], $values[$datetimeKey]);
        $this->assertEquals($text[_VALUE::ID->column()], $values[$textKey]);
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
        $valueModel = $this->makeValueModel();
        // check values created
        $string = $valueModel->find(
            ATTR_TYPE::STRING->valueTable(),
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::STRING->value()][_ATTR::ID->column()]
        );
        $integer = $valueModel->find(
            ATTR_TYPE::INTEGER->valueTable(),
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::INTEGER->value()][_ATTR::ID->column()]
        );
        $decimal = $valueModel->find(
            ATTR_TYPE::DECIMAL->valueTable(),
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::DECIMAL->value()][_ATTR::ID->column()]
        );
        $datetime = $valueModel->find(
            ATTR_TYPE::DATETIME->valueTable(),
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::DATETIME->value()][_ATTR::ID->column()]
        );
        $text = $valueModel->find(
            ATTR_TYPE::TEXT->valueTable(),
            $domainKey,
            $entityKey,
            $attributes[ATTR_TYPE::TEXT->value()][_ATTR::ID->column()]
        );

        $this->assertFalse($string);
        $this->assertFalse($integer);
        $this->assertFalse($decimal);
        $this->assertFalse($datetime);
        $this->assertFalse($text);

        $this->assertEquals([], $result->getValues());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleAttribute
     */
    public function handleAttribute_update()
    {
        $domainKey = 11;
        $attrName = 'test1';
        $attrKey = 22;
        $config = [
            _ATTR::NAME->column() => $attrName,
            'not_exist' => 'field',
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
            _ATTR::STRATEGY->column() => 'test_STRATEGY',
            _ATTR::SOURCE->column() => 'test_SOURCE',
            _ATTR::DEFAULT_VALUE->column() => 'test_DEFAULT_VALUE',
            _ATTR::DESCRIPTION->column() => 'test_DESCRIPTION',
        ];
        $record = [
            _ATTR::ID->column() => $attrKey,
            _ATTR::NAME->column() => $attrName
        ];
        $attrModel = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['findByName', 'updateByArray'])->getMock();
        $attrModel->method('findByName')->with($attrName, $domainKey)->willReturn($record);
        $attrModel->expects($this->once())->method('updateByArray')
            ->with($attrKey, [
                _ATTR::NAME->column() => $attrName,
                _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
                _ATTR::STRATEGY->column() => 'test_STRATEGY',
                _ATTR::SOURCE->column() => 'test_SOURCE',
                _ATTR::DEFAULT_VALUE->column() => 'test_DEFAULT_VALUE',
                _ATTR::DESCRIPTION->column() => 'test_DESCRIPTION',
            ]);

        $entityResult = $this->getMockBuilder(EntityFactoryResult::class)
            ->onlyMethods(['getDomainKey'])->getMock();
        $entityResult->method('getDomainKey')->willReturn($domainKey);

        $entityFactory = $this->getMockBuilder(EntityFactory::class)
            ->onlyMethods(['makeAttributeModel', 'getResult'])->getMock();
        $entityFactory->method('getResult')->willReturn($entityResult);
        $entityFactory->method('makeAttributeModel')->willReturn($attrModel);

        $entityFactory->handleAttribute($config);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Factory\EntityFactory::handlePivot
     */
    public function handlePivot_using_old_pivot_record()
    {
        $pivotKey = 33;
        $domainKey = 1;
        $setKey = 2;
        $groupKey = 3;
        $attrKey = 4;
        $pivotRecord = [
            _PIVOT::ID->column() => $pivotKey,
            _PIVOT::DOMAIN_ID->column() => $domainKey,
            _PIVOT::SET_ID->column() => $setKey,
            _PIVOT::GROUP_ID->column() => $groupKey,
            _PIVOT::ATTR_ID->column() => $attrKey
        ];
        $entityResult = $this->getMockBuilder(EntityFactoryResult::class)
            ->onlyMethods(['getDomainKey', 'getSetKey'])->getMock();
        $entityResult->method('getDomainKey')->willReturn($domainKey);
        $entityResult->method('getSetKey')->willReturn($setKey);
        $pivotModel = $this->getMockBuilder(PivotModel::class)
            ->onlyMethods(['findOne'])->getMock();
        $pivotModel->method('findOne')->willReturn($pivotRecord);

        $entityFactory = $this->getMockBuilder(EntityFactory::class)
            ->onlyMethods(['makePivotModel', 'getResult'])->getMock();
        $entityFactory->method('makePivotModel')->willReturn($pivotModel);
        $entityFactory->method('getResult')->willReturn($entityResult);

        $this->assertEquals($pivotKey, $entityFactory->handlePivot($attrKey, $groupKey));
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Factory\EntityFactory::handleValue
     */
    public function handleValue_update()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attrKey = 4;
        $attrType = ATTR_TYPE::INTEGER;
        $valueKey = 5;
        $record = [
            _VALUE::ID->column() => $valueKey
        ];
        $value = 432;
        $parsedValue = 433;

        $entityResult = $this->getMockBuilder(EntityFactoryResult::class)
            ->onlyMethods(['getDomainKey', 'getSetKey'])->getMock();
        $entityResult->method('getDomainKey')->willReturn($domainKey);

        $valueParser = $this->getMockBuilder(ValueParser::class)
            ->onlyMethods(['parse'])->getMock();
        $valueParser->expects($this->once())
            ->method('parse')
            ->with($attrType, $value)->willReturn($parsedValue);

        $valueModel = $this->getMockBuilder(ValueBase::class)
            ->onlyMethods(['find', 'update'])->getMock();
        $valueModel->method('find')->willReturn($record);
        $valueModel->expects($this->once())->method('update')
            ->with($attrType->valueTable(), $domainKey, $entityKey, $attrKey, $parsedValue);

        $entityFactory = $this->getMockBuilder(EntityFactory::class)
            ->onlyMethods(['makeValueModel', 'makeValueParser', 'getResult'])->getMock();
        $entityFactory->method('makeValueModel')->willReturn($valueModel);
        $entityFactory->method('makeValueParser')->willReturn($valueParser);
        $entityFactory->method('getResult')->willReturn($entityResult);

        $this->assertEquals($valueKey, $entityFactory->handleValue($attrType, $entityKey, $attrKey, $value));
    }
}
