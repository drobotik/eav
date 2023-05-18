<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderParser;

use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_CONFIG;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\QueryBuilder\QueryBuilderGroup;
use Drobotik\Eav\QueryBuilder\QueryBuilderManager;
use Drobotik\Eav\QueryBuilder\QueryBuilderParser;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use PHPUnit\Framework\TestCase;

class QueryBuilderParserFunctionalTest extends TestCase
{
    private QueryBuilderParser $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = new QueryBuilderParser();
        $manager = new QueryBuilderManager();
        $attributes = new QueryBuilderAttributes();
        $manager->setAttributesPivot($attributes);
        $this->parser->setManager($manager);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderParser::parse
     */
    public function parse_empty()
    {
        $result = $this->parser->parse([]);
        $this->assertInstanceOf(QueryBuilderGroup::class, $result);
        $group = new QueryBuilderGroup();
        $condition = $group->getCondition();
        $items = $group->getItems();
        $this->assertEquals($condition, $result->getCondition());
        $this->assertEquals($items, $result->getItems());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderParser::parse
     */
    public function parse_group_attributes()
    {
        $manager = new QueryBuilderManager();
        $attributes = new QueryBuilderAttributes();
        $manager->setAttributesPivot($attributes);
        $this->parser->setManager($manager);
        $group = $this->parser->parse([]);
        $this->assertSame($attributes , $group->getAttributes());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderParser::parse
     */
    public function parse_empty_with_condition()
    {
        $result = $this->parser->parse([
            QB_CONFIG::CONDITION => QB_CONDITION::OR
        ]);
        $this->assertEquals(QB_CONDITION::OR, $result->getCondition());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderParser::makeRule
     */
    public function make_rule()
    {
        $rule = $this->parser->makeRule([
            QB_CONFIG::NAME => 'price',
            QB_CONFIG::OPERATOR => QB_OPERATOR::LESS_OR_EQUAL->name(),
            QB_CONFIG::VALUE => 10.11
        ]);
        $this->assertEquals('price', $rule->getName());
        $this->assertEquals(QB_OPERATOR::LESS_OR_EQUAL, $rule->getOperator());
        $this->assertEquals(10.11, $rule->getValue());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderParser::parse
     */
    public function parse_simple_rules() {
        $result = $this->parser->parse([
            QB_CONFIG::CONDITION => QB_CONDITION::OR,
            QB_CONFIG::RULES => [
                [
                    QB_CONFIG::NAME => 'title',
                    QB_CONFIG::OPERATOR => QB_OPERATOR::EQUAL->name(),
                    QB_CONFIG::VALUE => 'water'
                ],
                [
                    QB_CONFIG::NAME => 'price',
                    QB_CONFIG::OPERATOR => QB_OPERATOR::LESS_OR_EQUAL->name(),
                    QB_CONFIG::VALUE => 10.11
                ]
            ]
        ]);
        $items = $result->getItems();
        $this->assertCount(2, $items);
        $this->assertEquals('title', $items[0]->getName());
        $this->assertEquals(QB_OPERATOR::EQUAL, $items[0]->getOperator());
        $this->assertEquals('water', $items[0]->getValue());

        $this->assertEquals('price', $items[1]->getName());
        $this->assertEquals(QB_OPERATOR::LESS_OR_EQUAL, $items[1]->getOperator());
        $this->assertEquals(10.11, $items[1]->getValue());

        $this->assertEquals(QB_CONDITION::OR, $result->getCondition());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderParser::parse
     */
    public function parse_nested_groups_with_rules() {
        $result = $this->parser->parse([
            QB_CONFIG::RULES => [
                [
                    QB_CONFIG::NAME => 'title',
                    QB_CONFIG::OPERATOR => QB_OPERATOR::EQUAL->name(),
                    QB_CONFIG::VALUE => 'water'
                ],
                [
                    QB_CONFIG::NAME => 'price',
                    QB_CONFIG::OPERATOR => QB_OPERATOR::LESS_OR_EQUAL->name(),
                    QB_CONFIG::VALUE => 10.11
                ],
                [
                    QB_CONFIG::CONDITION => QB_CONDITION::OR,
                    QB_CONFIG::RULES => [
                        [
                            QB_CONFIG::NAME => 'category',
                            QB_CONFIG::OPERATOR => QB_OPERATOR::EQUAL->name(),
                            QB_CONFIG::VALUE => 1
                        ],
                        [
                            QB_CONFIG::NAME => 'category',
                            QB_CONFIG::OPERATOR => QB_OPERATOR::EQUAL->name(),
                            QB_CONFIG::VALUE => 2
                        ],
                        [
                            QB_CONFIG::CONDITION => QB_CONDITION::AND,
                            QB_CONFIG::RULES => [
                                [
                                    QB_CONFIG::NAME => 'size',
                                    QB_CONFIG::OPERATOR => QB_OPERATOR::LESS_OR_EQUAL->name(),
                                    QB_CONFIG::VALUE => 15
                                ],
                                [
                                    QB_CONFIG::NAME => 'length',
                                    QB_CONFIG::OPERATOR => QB_OPERATOR::GREATER_OR_EQUAL->name(),
                                    QB_CONFIG::VALUE => 5
                                ],
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $items = $result->getItems();
        $this->assertCount(3, $items);
        $group = $items[2];
        $this->assertInstanceOf(QueryBuilderGroup::class, $group);
        $this->assertEquals(QB_CONDITION::OR, $group->getCondition());
        $items = $group->getItems();
        $this->assertCount(3, $items);
        $this->assertEquals('category', $items[0]->getName());
        $this->assertEquals(QB_OPERATOR::EQUAL, $items[0]->getOperator());
        $this->assertEquals(1, $items[0]->getValue());
        $this->assertEquals('category', $items[1]->getName());
        $this->assertEquals(QB_OPERATOR::EQUAL, $items[1]->getOperator());
        $this->assertEquals(2, $items[1]->getValue());
        $group = $items[2];
        $this->assertInstanceOf(QueryBuilderGroup::class, $group);
        $items = $group->getItems();
        $this->assertCount(2, $items);
        $this->assertEquals('size', $items[0]->getName());
        $this->assertEquals(QB_OPERATOR::LESS_OR_EQUAL, $items[0]->getOperator());
        $this->assertEquals(15, $items[0]->getValue());
        $this->assertEquals('length', $items[1]->getName());
        $this->assertEquals(QB_OPERATOR::GREATER_OR_EQUAL, $items[1]->getOperator());
        $this->assertEquals(5, $items[1]->getValue());
    }
}