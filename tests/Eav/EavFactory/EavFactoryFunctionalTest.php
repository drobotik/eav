<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EavFactory;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Enum\_SET;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Result\EntityFactoryResult;
use Drobotik\Eav\Result\Result;
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
     * @covers \Drobotik\Eav\Factory\EavFactory::createDomain
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
     * @covers \Drobotik\Eav\Factory\EavFactory::createDomain
     */
    public function domainInputData()
    {
        $name = 'test';
        $key = $this->eavFactory->createDomain([
            _DOMAIN::NAME->column() => $name
        ]);

        $qb = Connection::get()->createQueryBuilder();
        $record = $qb->select('*')->from(_DOMAIN::table())
            ->executeQuery()
            ->fetchAssociative();

        $this->assertEquals([
            _DOMAIN::ID->column() => $key,
            _DOMAIN::NAME->column() => $name
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createEntity
     */
    public function entityDefault()
    {
        $this->assertEquals(1, $this->eavFactory->createEntity());

        $qb = Connection::get()->createQueryBuilder();
        $record = $qb->select('*')->from(_ENTITY::table())
            ->executeQuery()
            ->fetchAssociative();

        $this->assertEquals([
            _ENTITY::ID->column() => 1,
            _ENTITY::DOMAIN_ID->column() => 1,
            _ENTITY::ATTR_SET_ID->column() => 1,
            _ENTITY::SERVICE_KEY->column() => null
        ], $record);

        // domain created
        $record = $qb->select('count(*) as c')->from(_DOMAIN::table())
            ->executeQuery()
            ->fetchAssociative();
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
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttributeSet
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
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttributeSet
     */
    public function attributeSetInput()
    {
        $input = [
            _SET::NAME->column() => 'test',
        ];
        $setKey = $this->eavFactory->createAttributeSet(123, $input);

        $qb = Connection::get()->createQueryBuilder();
        $record = $qb->select('*')->from(_SET::table())
            ->executeQuery()
            ->fetchAssociative();

        $this->assertEquals([
            _SET::ID->column() => $setKey,
            _SET::DOMAIN_ID->column() => 123,
            _SET::NAME->column() => 'test'
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createGroup
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
     * @covers \Drobotik\Eav\Factory\EavFactory::createGroup
     */
    public function attributeGroupInput()
    {
        $input = [
            _GROUP::NAME->column() => 'test',
        ];

        $groupKey = $this->eavFactory->createGroup(123, $input);

        $qb = Connection::get()->createQueryBuilder();
        $record = $qb->select('*')->from(_GROUP::table())
            ->executeQuery()
            ->fetchAssociative();

        $this->assertEquals([
            _GROUP::ID->column() => $groupKey,
            _GROUP::SET_ID->column() => 123,
            _GROUP::NAME->column() => 'test'
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttribute
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
     * @covers \Drobotik\Eav\Factory\EavFactory::createAttribute
     */
    public function attributeInput()
    {
        $domainKey = 123;

        $input = [
            _ATTR::NAME->column() => 'test',
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
            _ATTR::STRATEGY->column() => 'strategy',
            _ATTR::SOURCE->column() => 'source',
            _ATTR::DEFAULT_VALUE->column() => 'default',
            _ATTR::DESCRIPTION->column() => 'description',
        ];
        $key = $this->eavFactory->createAttribute($domainKey, $input);

        $qb = Connection::get()->createQueryBuilder();
        $record = $qb->select('*')->from(_ATTR::table())
            ->executeQuery()
            ->fetchAssociative();

        $this->assertEquals([
            _ATTR::ID->column() => $key,
            _ATTR::DOMAIN_ID->column() => 123,
            _ATTR::NAME->column() => 'test',
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
            _ATTR::STRATEGY->column() => 'strategy',
            _ATTR::SOURCE->column() => 'source',
            _ATTR::DEFAULT_VALUE->column() => 'default',
            _ATTR::DESCRIPTION->column() => 'description',
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createPivot
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

        $qb = Connection::get()->createQueryBuilder();
        $record = $qb->select('*')->from(_PIVOT::table())
            ->executeQuery()
            ->fetchAssociative();

        $this->assertEquals([
            _PIVOT::ID->column() => $pivotKey,
            _PIVOT::DOMAIN_ID->column() => $domainKey,
            _PIVOT::SET_ID->column() => $setKey,
            _PIVOT::GROUP_ID->column() => $groupKey,
            _PIVOT::ATTR_ID->column() => $attributeKey
        ], $record);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Factory\EavFactory::createEavEntity
     */
    public function createEavEntity()
    {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);
        $config = [
            [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::STRING->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::STRING->randomValue(),
                ],
                ATTR_FACTORY::GROUP->field() => $groupKey
            ]
        ];
        $result = $this->eavFactory->createEavEntity($config, $domainKey, $setKey);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
        $this->assertInstanceOf(EntityFactoryResult::class, $result->getData());
    }
}
