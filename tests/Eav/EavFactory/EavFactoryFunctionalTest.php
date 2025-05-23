<?php
/**
 * This file is part of the eav package.
 *
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EavFactory;

use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_DOMAIN;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Enum\_GROUP;
use Kuperwood\Eav\Enum\_PIVOT;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Enum\_SET;
use Kuperwood\Eav\Enum\ATTR_FACTORY;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Result\EntityFactoryResult;
use Kuperwood\Eav\Result\Result;
use Faker\Generator;
use PDO;
use ReflectionClass;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class EavFactoryFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::__construct
     */
    public function faker()
    {
        $reflectionClass = new ReflectionClass($this->eavFactory);
        $reflectionProperty = $reflectionClass->getProperty('faker');
        $reflectionProperty->setAccessible(true); // Make the private property accessible
        $propertyValue = $reflectionProperty->getValue($this->eavFactory);
        $this->assertInstanceOf(Generator::class, $reflectionProperty->getValue($this->eavFactory));

    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createDomain
     */
    public function domainDefault()
    {
        $this->assertEquals(1, $this->eavFactory->createDomain());
        $this->assertEquals(2, $this->eavFactory->createDomain());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createDomain
     */
    public function domainInputData()
    {
        $name = 'test';
        $key = $this->eavFactory->createDomain([
            _DOMAIN::NAME => $name
        ]);

        $sql = sprintf("SELECT * FROM %s", _DOMAIN::table());
        $stmt =  Connection::get()->prepare($sql);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals([
            _DOMAIN::ID => $key,
            _DOMAIN::NAME => $name
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createEntity
     */
    public function entityDefault()
    {
        $this->assertEquals(1, $this->eavFactory->createEntity());

        $sql = sprintf("SELECT * FROM %s", _ENTITY::table());
        $stmt =  Connection::get()->prepare($sql);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals([
            _ENTITY::ID => 1,
            _ENTITY::DOMAIN_ID => 1,
            _ENTITY::ATTR_SET_ID => 1,
            _ENTITY::SERVICE_KEY => null
        ], $record);

        $sql = sprintf("SELECT count(*) as c FROM %s", _DOMAIN::table());
        $stmt =  Connection::get()->prepare($sql);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(1, $record['c']);

        // attribute set created
        $model = new AttributeSetModel();
        $this->assertEquals(1, $model->count());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createAttributeSet
     */
    public function attributeSet()
    {
        $setKey = $this->eavFactory->createAttributeSet();
        $this->assertEquals(1, $setKey);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createAttributeSet
     */
    public function attributeSetInput()
    {
        $input = [
            _SET::NAME => 'test',
        ];
        $setKey = $this->eavFactory->createAttributeSet(123, $input);

        $sql = sprintf("SELECT * FROM %s", _SET::table());
        $stmt =  Connection::get()->prepare($sql);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals([
            _SET::ID => $setKey,
            _SET::DOMAIN_ID => 123,
            _SET::NAME => 'test'
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createGroup
     */
    public function attributeGroup()
    {
        $this->assertEquals(1, $this->eavFactory->createGroup());
        $this->assertEquals(2, $this->eavFactory->createGroup());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createGroup
     */
    public function attributeGroupInput()
    {
        $input = [
            _GROUP::NAME => 'test',
        ];

        $groupKey = $this->eavFactory->createGroup(123, $input);

        $sql = sprintf("SELECT * FROM %s", _GROUP::table());
        $stmt =  Connection::get()->prepare($sql);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals([
            _GROUP::ID => $groupKey,
            _GROUP::SET_ID => 123,
            _GROUP::NAME => 'test'
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createAttribute
     */
    public function attribute()
    {
        $this->assertEquals(1, $this->eavFactory->createAttribute());
        $this->assertEquals(2, $this->eavFactory->createAttribute());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createAttribute
     */
    public function attributeInput()
    {
        $domainKey = 123;

        $input = [
            _ATTR::NAME => 'test',
            _ATTR::TYPE => ATTR_TYPE::INTEGER,
            _ATTR::STRATEGY => 'strategy',
            _ATTR::SOURCE => 'source',
            _ATTR::DEFAULT_VALUE => 'default',
            _ATTR::DESCRIPTION => 'description',
        ];
        $key = $this->eavFactory->createAttribute($domainKey, $input);

        $sql = sprintf("SELECT * FROM %s", _ATTR::table());
        $stmt =  Connection::get()->prepare($sql);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals([
            _ATTR::ID => $key,
            _ATTR::DOMAIN_ID => 123,
            _ATTR::NAME => 'test',
            _ATTR::TYPE => ATTR_TYPE::INTEGER,
            _ATTR::STRATEGY => 'strategy',
            _ATTR::SOURCE => 'source',
            _ATTR::DEFAULT_VALUE => 'default',
            _ATTR::DESCRIPTION => 'description',
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createPivot
     */
    public function pivot()
    {
        $this->eavFactory->createDomain();
        $domainKey = $this->eavFactory->createDomain();
        $this->eavFactory->createAttributeSet($domainKey);
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $this->eavFactory->createGroup($setKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $this->eavFactory->createAttribute($domainKey);
        $attributeKey = $this->eavFactory->createAttribute($domainKey);
        $pivotKey = $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $attributeKey);

        $sql = sprintf("SELECT * FROM %s", _PIVOT::table());
        $stmt =  Connection::get()->prepare($sql);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals([
            _PIVOT::ID => $pivotKey,
            _PIVOT::DOMAIN_ID => $domainKey,
            _PIVOT::SET_ID => $setKey,
            _PIVOT::GROUP_ID => $groupKey,
            _PIVOT::ATTR_ID => $attributeKey
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Kuperwood\Eav\Factory\EavFactory::createEavEntity
     */
    public function createEavEntity()
    {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $config = [
            [
                ATTR_FACTORY::ATTRIBUTE => [
                    _ATTR::NAME => ATTR_TYPE::STRING,
                    _ATTR::TYPE => ATTR_TYPE::STRING,
                    _ATTR::DEFAULT_VALUE => ATTR_TYPE::randomValue(ATTR_TYPE::STRING),
                ],
                ATTR_FACTORY::GROUP => $groupKey
            ]
        ];
        $result = $this->eavFactory->createEavEntity($config, $domainKey, $setKey);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::CREATED), $result->getMessage());
        $this->assertInstanceOf(EntityFactoryResult::class, $result->getData());
    }
}
