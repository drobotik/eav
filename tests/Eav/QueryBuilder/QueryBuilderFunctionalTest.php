<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilder;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_CONFIG;
use Drobotik\Eav\Enum\QB_JOIN;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\QueryBuilder\Config;
use Drobotik\Eav\QueryBuilder\Expression;
use Drobotik\Eav\QueryBuilder\QueryBuilder;
use Tests\QueryingDataTestCase;

class QueryBuilderFunctionalTest extends QueryingDataTestCase
{
    private QueryBuilder $qb;

    public function setUp(): void
    {
        parent::setUp();
        $this->qb = new QueryBuilder();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::setConfig
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::getConfig
     */
    public function config()
    {
        $config = new Config();
        $this->qb->setConfig($config);
        $this->assertSame($config, $this->qb->getConfig());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::getQuery
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::__construct
     */
    public function get_query()
    {
        $query = $this->qb->getQuery();
        $this->assertInstanceOf(\Doctrine\DBAL\Query\QueryBuilder::class, $query);
        $this->assertEquals("SELECT ", $query->getSQL());
        $this->assertEquals([], $query->getParameters());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::startQuery
     */
    public function startQuery()
    {
        $config = new Config();
        $this->qb->setConfig($config);
        $this->qb->startQuery();
        $sql = sprintf("SELECT e.%s FROM %s e", _ENTITY::ID, _ENTITY::table());
        $query = $this->qb->getQuery();
        $this->assertEquals($sql, $query->getSQL());
        $this->assertEquals([], $query->getParameters());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::startQuery
     */
    public function startQueryWithJoinsAndSelects()
    {
        $config = new Config;
        $config->addJoin('table1', 'name1', 23);
        $config->addJoin('table2', 'name2', 24);
        $config->addSelect('name1');
        $config->addSelect('name2');
        $qb = $this->getMockBuilder(QueryBuilder::class)
            ->onlyMethods(['join', 'select'])->getMock();
        $qb->setConfig($config);
        $qb->expects($this->exactly(2))
            ->method('join')
            ->withConsecutive(['name1'],['name2']);
        $qb->expects($this->exactly(2))
            ->method('select')
            ->withConsecutive(['name1'],['name2']);
        $qb->startQuery();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::endQuery
     */
    public function endQuery()
    {
        $setKey = 321;
        $domainKey = 123;
        $config = new Config;
        $config->setDomainKey($domainKey);
        $config->setSetKey($setKey);
        $domainParam = $config->getDomainKeyParam();
        $setParam = $config->getSetKeyParam();

        $this->qb->setConfig($config);
        $this->qb->endQuery();
        $sql = sprintf("SELECT  WHERE (e.%s = :%s) AND (e.%s = :%s)",
            _ENTITY::DOMAIN_ID,
            $domainParam,
            _ENTITY::ATTR_SET_ID,
            $setParam
        );

        $query = $this->qb->getQuery();
        $this->assertEquals($sql, $query->getSQL());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::applyParams
     */
    public function applyParams()
    {
        $config = new Config();
        $config->setSetKey(12);
        $config->setDomainKey(23);
        $config->createParameter('test', 'value');

        $domainParam = $config->getDomainKeyParam();
        $setParam = $config->getSetKeyParam();

        $this->qb->setConfig($config);
        $this->qb->applyParams();

        $this->assertEquals([
            $domainParam => 23,
            $setParam => 12,
            'test' => 'value'
        ], $this->qb->getQuery()->getParameters());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::applyExpressions
     */
    public function applyExpressions()
    {
        $expr1 = new Expression();
        $expr1->setField('field1');
        $expr1->setOperator(QB_OPERATOR::EQUAL);
        $expr1->setParam1('field1Val');

        $expression2 = new Expression();
        $expression2->setField('field2');
        $expression2->setOperator(QB_OPERATOR::LESS);
        $expression2->setParam1('field2Val');

        $expression3 = new Expression();
        $expression3->setField('field3');
        $expression3->setOperator(QB_OPERATOR::CONTAINS);
        $expression3->setParam1('field3Val');

        $expressions = [
            QB_CONDITION::AND,
            $expr1,
            [
                QB_CONDITION::OR,
                $expression2,
                $expression3
            ]
        ];
        $this->qb->applyExpressions($expressions);
        $this->assertEquals(
            "SELECT  WHERE ((field2 < :field2Val) OR (field3 LIKE :field3Val)) AND (field1 = :field1Val)",
            $this->qb->getQuery()->getSQL()
        );
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::run
     */
    public function method_run()
    {
        $domainKey = 1;
        $setKey = 1;
        $attrDecimalKey = 3;
        $attrStringKey = 1;
        $attrDecimalName = ATTR_TYPE::DECIMAL->value();
        $attrStringName = ATTR_TYPE::STRING->value();
        $valueDecimalTable = ATTR_TYPE::DECIMAL->valueTable();
        $valueStringTable = ATTR_TYPE::STRING->valueTable();

        $decimalParamValue = 10000;
        $stringParamValue1 = '%et dolores%';
        $stringParamValue2 = 'sit quisquam';

        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['getExpressions'])->getMock();

        $decimalParamName = $config->createParameter($attrDecimalName, $decimalParamValue);
        $expr1 = new Expression();
        $expr1->setField($attrDecimalName);
        $expr1->setOperator(QB_OPERATOR::LESS);
        $expr1->setParam1($decimalParamName);

        $stringParamName1 = $config->createParameter($attrStringName, $stringParamValue1);
        $expr2 = new Expression();
        $expr2->setField($attrStringName);
        $expr2->setOperator(QB_OPERATOR::CONTAINS);
        $expr2->setParam1($stringParamName1);

        $expr3 = new Expression();
        $stringParamName2 = $config->createParameter($attrStringName, $stringParamValue2);
        $expr3->setField($attrStringName);
        $expr3->setOperator(QB_OPERATOR::EQUAL);
        $expr3->setParam1($stringParamName2);

        $expressions = [
            QB_CONDITION::AND,
            $expr1,
            [
                QB_CONDITION::OR,
                $expr2,
                $expr3
            ]
        ];
        $config->method('getExpressions')->willReturn($expressions);

        $config->setDomainKey($domainKey);
        $config->setSetKey($setKey);

        $domainParam = $config->getDomainKeyParam();
        $setParam = $config->getSetKeyParam();

        $config->addSelect($attrStringName);
        $config->addSelect($attrDecimalName);

        $config->addJoin($valueDecimalTable, $attrDecimalName, $attrDecimalKey);
        $config->addJoin($valueStringTable, $attrStringName, $attrStringKey);

        $joinDecimalParam = $config->getJoin($attrDecimalName)[QB_JOIN::ATTR_PARAM];
        $joinStringParam = $config->getJoin($attrStringName)[QB_JOIN::ATTR_PARAM];

        $this->qb->setConfig($config);
        $this->qb->run();

        $entityCol = _ENTITY::ID;
        $entitySetCol = _ENTITY::ATTR_SET_ID;
        $entityDomainCol = _ENTITY::DOMAIN_ID;
        $attributeCol = _VALUE::ATTRIBUTE_ID;

        $query = $this->qb->getQuery();

        $this->assertEquals(
            "SELECT e.$entityCol, $attrStringName.value as $attrStringName, $attrDecimalName.value as $attrDecimalName FROM eav_entities e INNER JOIN $valueDecimalTable $attrDecimalName ON e.$entityCol = $attrDecimalName.$entityCol AND $attrDecimalName.$attributeCol = :$joinDecimalParam INNER JOIN $valueStringTable $attrStringName ON e.$entityCol = $attrStringName.$entityCol AND $attrStringName.$attributeCol = :$joinStringParam WHERE (($attrStringName LIKE :$stringParamName1) OR ($attrStringName = :$stringParamName2)) AND ($attrDecimalName < :$decimalParamName) AND (e.$entityDomainCol = :$domainParam) AND (e.$entitySetCol = :$setParam)",
            $query->getSQL()
        );

        $this->assertEquals([
            $joinDecimalParam => $attrDecimalKey,
            $joinStringParam => $attrStringKey,
            $domainParam => $domainKey,
            $setParam => $setKey,
            $decimalParamName => $decimalParamValue,
            $stringParamName1 => $stringParamValue1,
            $stringParamName2 => $stringParamValue2
        ], $query->getParameters());
        $this->assertSame([
            [
                $entityCol => 1822,
                $attrStringName => 'et dolores',
                $attrDecimalName => 170.359
            ],
            [
                $entityCol => 18795,
                $attrStringName => 'sit quisquam',
                $attrDecimalName => 3685.969
            ],
            [
                $entityCol => 19738,
                $attrStringName => 'sit quisquam',
                $attrDecimalName => 180.63
            ]
        ], $query->executeQuery()->fetchAllAssociative());

    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::run
     */
    public function run_with_parsed_config()
    {
        $filters = [
            QB_CONFIG::CONDITION => QB_CONDITION::AND,
            QB_CONFIG::RULES => [
                [
                    QB_CONFIG::NAME => ATTR_TYPE::DECIMAL->value(),
                    QB_CONFIG::OPERATOR => QB_OPERATOR::LESS->name(),
                    QB_CONFIG::VALUE => 10000
                ],
                [
                    QB_CONFIG::CONDITION => QB_CONDITION::OR,
                    QB_CONFIG::RULES => [
                        [
                            QB_CONFIG::NAME => ATTR_TYPE::STRING->value(),
                            QB_CONFIG::OPERATOR => QB_OPERATOR::CONTAINS->name(),
                            QB_CONFIG::VALUE => 'sit quisquam'
                        ],
                        [
                            QB_CONFIG::NAME => ATTR_TYPE::STRING->value(),
                            QB_CONFIG::OPERATOR => QB_OPERATOR::EQUAL->name(),
                            QB_CONFIG::VALUE => 'et dolores'
                        ]
                    ],
                ]
            ],
        ];
        $domainKey = 1;
        $setKey = 1;
        $columns = [ATTR_TYPE::STRING->value(), ATTR_TYPE::DECIMAL->value()];
        $qb = new QueryBuilder();
        $config = new Config();
        $config->setDomainKey($domainKey);
        $config->setSetKey($setKey);
        $config->addAttributes((new AttributeSetModel())->findAttributes($domainKey, $setKey));
        $config->addColumns($columns);
        $config->parse($filters);
        $qb->setConfig($config);
        $this->assertSame([
            [
                _ENTITY::ID => 1822,
                ATTR_TYPE::STRING->value() => 'et dolores',
                ATTR_TYPE::DECIMAL->value() => 170.359
            ],
            [
                _ENTITY::ID => 18795,
                ATTR_TYPE::STRING->value() => 'sit quisquam',
                ATTR_TYPE::DECIMAL->value() => 3685.969
            ],
            [
                _ENTITY::ID => 19738,
                ATTR_TYPE::STRING->value() => 'sit quisquam',
                ATTR_TYPE::DECIMAL->value() => 180.63
            ]
        ], $qb->run()->executeQuery()->fetchAllAssociative());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::select
     */
    public function select()
    {
        $this->qb->select('test');
        $this->assertEquals('SELECT test.value as test', $this->qb->getQuery()->getSQL());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::join
     */
    public function join()
    {
        $table = 'test_table';
        $field = 'test_field';
        $config = new Config();
        $config->addJoin($table, $field, 1);
        $join = $config->getJoin($field);
        $paramName = $join[QB_JOIN::ATTR_PARAM];

        $qb = $this->getMockBuilder(QueryBuilder::class)
            ->onlyMethods(['getQuery'])->getMock();

        $query = Connection::get()->createQueryBuilder()->from('entity', 'e');
        $qb->method('getQuery')->willReturn($query);

        $qb->setConfig($config);

        $q = $qb->join($field);
        $on = sprintf('e.%s = %s.%s AND %s.%s = :%s',
            _ENTITY::ID,
            $field,
            _VALUE::ENTITY_ID,
            $field,
            _VALUE::ATTRIBUTE_ID,
            $paramName
        );
        $join = "SELECT  FROM entity e INNER JOIN $table $field ON ";
        $this->assertEquals($join.$on, $q->getSQL());
    }
}