<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilder;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_CONFIG;
use Drobotik\Eav\Enum\QB_JOIN;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Exception\QueryBuilderException;
use Drobotik\Eav\QueryBuilder\Config;
use Drobotik\Eav\QueryBuilder\Expression;
use Drobotik\Eav\Value\ValueEmpty;
use Tests\QueryingDataTestCase;

class QueryBuilderConfigFunctionalTest extends QueryingDataTestCase
{
    private Config $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = new Config();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Config::getDomainKeyParam
     * @covers \Drobotik\Eav\QueryBuilder\Config::setDomainKey
     */
    public function domain_key()
    {
        $this->config->setDomainKey(12);
        $this->assertEquals('domainKey' , $this->config->getDomainKeyParam());
        $this->assertEquals(12 , $this->config->getParameterValue('domainKey'));
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Config::getSetKeyParam
     * @covers \Drobotik\Eav\QueryBuilder\Config::setSetKey
     */
    public function set_key()
    {
        $this->config->setSetKey(12);
        $this->assertEquals('setKey' , $this->config->getSetKeyParam());
        $this->assertEquals(12 , $this->config->getParameterValue('setKey'));
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Config::hasJoin
     * @covers \Drobotik\Eav\QueryBuilder\Config::addJoin
     * @covers \Drobotik\Eav\QueryBuilder\Config::getJoins
     * @covers \Drobotik\Eav\QueryBuilder\Config::getJoin
     */
    public function joins()
    {
        $table = 'tbl';
        $name = 'test';
        $param = sprintf('%s_join_%s', $name, QB_JOIN::ATTR_PARAM);
        $attrKey = 234;
        $this->assertEquals([], $this->config->getJoins());
        $this->assertFalse($this->config->hasJoin($name));
        $this->config->addJoin($table, $name, $attrKey);
        $this->assertTrue($this->config->hasJoin($name));
        $join = [
            QB_JOIN::TABLE => $table,
            QB_JOIN::NAME => $name,
            QB_JOIN::ATTR_PARAM => $param
        ];
        $this->assertEquals([$name => $join], $this->config->getJoins());
        $this->assertEquals($join, $this->config->getJoin('test'));

        $this->assertEquals([
            $param => $attrKey
        ], $this->config->getParameters());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Config::addColumns
     * @covers \Drobotik\Eav\QueryBuilder\Config::getColumns
     */
    public function columns()
    {
        $this->assertEquals([], $this->config->getColumns());
        $this->config->addColumns([123,321]);
        $this->assertEquals([123,321], $this->config->getColumns());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Config::addColumns
     * @covers \Drobotik\Eav\QueryBuilder\Config::getColumns
     */
    public function columns_unique()
    {
        $this->config->addColumns(['a','a','a','b']);
        $this->assertEquals(['a','b'], $this->config->getColumns());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Config::hasAttribute
     * @covers \Drobotik\Eav\QueryBuilder\Config::addAttribute
     * @covers \Drobotik\Eav\QueryBuilder\Config::getAttribute
     * @covers \Drobotik\Eav\QueryBuilder\Config::getAttributes
     * @covers \Drobotik\Eav\QueryBuilder\Config::addAttributes
     */
    public function attributes()
    {
        $this->assertEquals([], $this->config->getAttributes());
        $this->assertFalse($this->config->hasAttribute('test'));
        $attribute = [_ATTR::NAME->column() => 'test'];
        $this->config->addAttribute($attribute);
        $this->assertTrue($this->config->hasAttribute('test'));
        $this->assertSame($attribute, $this->config->getAttribute('test'));
        $this->assertEquals(['test' => $attribute], $this->config->getAttributes());

        $extraAttr1 = [_ATTR::NAME->column() => 'test2'];
        $extraAttr2 = [_ATTR::NAME->column() => 'test3'];
        $this->config->addAttributes([$extraAttr1, $extraAttr2]);
        $this->assertEquals([
            'test' => $attribute,
            'test2' => $extraAttr1,
            'test3' => $extraAttr2
        ], $this->config->getAttributes());

    }
    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::isSelected
     * @covers \Drobotik\Eav\QueryBuilder\Config::addSelect
     * @covers \Drobotik\Eav\QueryBuilder\Config::getSelected
     */
    public function selected()
    {
        $this->assertEquals([], $this->config->getSelected());
        $this->assertFalse($this->config->isSelected('test'));
        $this->config->addSelect('test');
        $this->assertTrue($this->config->isSelected('test'));
        $this->assertEquals(['test'], $this->config->getSelected());
    }
    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::getSelected
     */
    public function selected_sorted()
    {
        $selected = ['h', 'c', 'c', 'b', 'z', 'z'];
        $columns = ['a', 'b', 'c'];
        $this->config->addColumns($columns);
        foreach ($selected as $select)
        {
            $this->config->addSelect($select);
        }

        $this->assertEquals(['b','c','h','z'], $this->config->getSelected());
    }
    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::hasParameter
     * @covers \Drobotik\Eav\QueryBuilder\Config::createParameter
     * @covers \Drobotik\Eav\QueryBuilder\Config::getParameters
     * @covers \Drobotik\Eav\QueryBuilder\Config::getParameterValue
     */
    public function parameters()
    {
        $this->assertEquals([], $this->config->getParameters());
        $this->assertFalse($this->config->hasParameter('test'));
        $name = $this->config->createParameter('test', 'value');
        $this->assertEquals('test', $name);
        $this->assertTrue($this->config->hasParameter('test'));
        $this->assertEquals(['test' => 'value'], $this->config->getParameters());
        $this->assertEquals('value', $this->config->getParameterValue('test'));
    }
    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::createParameter
     */
    public function unique_parameter()
    {
        $this->config->createParameter('name', 'value');
        $name = $this->config->createParameter('name', 'value2');
        $this->assertNotEquals('name', $name);
        $this->assertMatchesRegularExpression('/name_[a-f0-9]{5}/', $name);
        $this->assertTrue($this->config->hasParameter($name));
    }

    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::handleColumns
     */
    public function handle_columns_has_no_attribute_exception()
    {
        $this->config->addColumns(['test']);

        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage(sprintf(QueryBuilderException::UNSUPPORTED_ATTRIBUTE, 'test'));

        $this->config->handleColumns();
    }

    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::handleColumns
     */
    public function handle_columns_attribute__type_exception()
    {
        $attribute = [
            _ATTR::ID->column() => 1,
            _ATTR::NAME->column() => 'test',
            _ATTR::TYPE->column() => 'wrong_type'
        ];

        $this->config->addColumns(['test']);
        $this->config->addAttribute($attribute);

        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNSUPPORTED_TYPE, 'wrong_type'));

        $this->config->handleColumns();
    }

    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::handleColumns
     */
    public function handle_columns()
    {
        $attrKey = 123;
        $attrName = 'test';
        $paramName = $attrName.'_join_'.QB_JOIN::ATTR_PARAM;

        $attribute = [
            _ATTR::ID->column() => $attrKey,
            _ATTR::NAME->column() => $attrName,
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
        ];

        $this->config->addColumns([$attrName, $attrName, $attrName]);
        $this->config->addAttribute($attribute);

        $this->config->handleColumns();

        $this->assertTrue($this->config->hasJoin($attrName));
        $this->assertEquals([$attrName], $this->config->getSelected());
        $this->assertEquals([$paramName => 123], $this->config->getParameters());
    }
    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::handleGroup
     */
    public function handle_group_unsupported_condition()
    {
        $group = [
            QB_CONFIG::CONDITION => 'test',
            QB_CONFIG::RULES => []
        ];
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage(sprintf(QueryBuilderException::UNSUPPORTED_CONDITION, 'test'));
        $this->config->handleGroup($group);
    }

    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::handleGroup
     */
    public function handle_group()
    {
        $filter = [
            QB_CONFIG::CONDITION => QB_CONDITION::AND->name(),
            QB_CONFIG::RULES => [
                ["test1"],
                [
                    QB_CONFIG::CONDITION => QB_CONDITION::OR->name(),
                    QB_CONFIG::RULES => [
                        ["test2"],
                        ["test3"],
                    ]
                ]
            ]
        ];

        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['handleRule'])->getMock();

        $expr1 = new Expression();
        $expr2 = new Expression();
        $expr3 = new Expression();

        $config->expects($this->exactly(3))->method('handleRule')
            ->withConsecutive([['test1']], [['test2']], [['test3']])
            ->willReturn($expr1, $expr2, $expr3);

        $result = $config->handleGroup($filter);

        $this->assertSame([
            QB_CONDITION::AND,
            $expr1,
            [
                QB_CONDITION::OR,
                $expr2,
                $expr3
            ]
        ], $result);
    }

    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::handleRule
     */
    public function handle_rule_unsupported_operator()
    {
        $rule = [
            QB_CONFIG::NAME => 'name',
            QB_CONFIG::OPERATOR => 'test',
            QB_CONFIG::VALUE => 1
        ];

        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage(sprintf(QueryBuilderException::UNSUPPORTED_OPERATOR, 'test'));
        $this->config->handleRule($rule);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::handleRule
     */
    public function handle_rule_without_value()
    {
        $rule = [
            QB_CONFIG::NAME => 'size',
            QB_CONFIG::OPERATOR => QB_OPERATOR::IS_NULL->name(),
        ];
        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['createExpression', 'registerSelect', 'registerJoin'])
            ->getMock();
        $config
            ->expects($this->once())
            ->method('createExpression')
            ->with(
                'size',
                QB_OPERATOR::IS_NULL,
                $this->isInstanceOf(ValueEmpty::class)
            );
        $config->handleRule($rule);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::handleRule
     */
    public function handle_rule()
    {
        $rule = [
            QB_CONFIG::NAME => 'size',
            QB_CONFIG::OPERATOR => QB_OPERATOR::EQUAL->name(),
            QB_CONFIG::VALUE => 12
        ];

        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['createExpression', 'registerSelect', 'registerJoin'])
            ->getMock();
        $config
            ->expects($this->once())
            ->method('createExpression')
            ->with('size', QB_OPERATOR::EQUAL, 12);
        $config
            ->expects($this->once())
            ->method('registerJoin')
            ->with('size');
        $config
            ->expects($this->once())
            ->method('registerSelect')
            ->with('size');

        $config->handleRule($rule);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::createExpression
     */
    public function create_expression()
    {
        $field = 'size';
        $fieldParam = $field.'_cond';
        $operator = QB_OPERATOR::EQUAL;
        $value = 2;

        $result = $this->config->createExpression($field, $operator, $value);
        $this->assertInstanceOf(Expression::class, $result);
        $this->assertEquals($field, $result->getField());
        $this->assertEquals($operator, $result->getOperator());
        $this->assertEquals(':'.$fieldParam, $result->getParam1());
        $this->assertEquals($value, $this->config->getParameterValue($fieldParam));
        $this->assertFalse($result->isParam2());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::createExpression
     */
    public function create_expression_between()
    {
        $field = 'size';
        $fieldParam1 = $field.'_cond_1';
        $fieldParam2 = $field.'_cond_2';
        $value = [2, 4];

        foreach ([QB_OPERATOR::BETWEEN, QB_OPERATOR::NOT_BETWEEN] as $operator)
        {
            $config = new Config();
            $result = $config->createExpression($field, $operator, $value);
            $this->assertInstanceOf(Expression::class, $result);
            $this->assertEquals($field, $result->getField());
            $this->assertEquals($operator, $result->getOperator());
            $this->assertEquals(':'.$fieldParam1, $result->getParam1());
            $this->assertEquals(':'.$fieldParam2, $result->getParam2());
            $this->assertEquals($value[0], $config->getParameterValue($fieldParam1));
            $this->assertEquals($value[1], $config->getParameterValue($fieldParam2));
        }
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::createExpression
     */
    public function create_expression_empty()
    {
        $field = 'size';
        $fieldParam = $field.'_cond';
        $value = new ValueEmpty();

        foreach ([QB_OPERATOR::IS_EMPTY, QB_OPERATOR::IS_NOT_EMPTY] as $operator)
        {
            $config = new Config();
            $result = $config->createExpression($field, $operator, $value);
            $this->assertInstanceOf(Expression::class, $result);
            $this->assertEquals($field, $result->getField());
            $this->assertEquals($operator, $result->getOperator());
            $this->assertEquals(':'.$fieldParam, $result->getParam1());
            $this->assertEquals('', $config->getParameterValue($fieldParam));
            $this->assertFalse($result->isParam2());
        }
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::createExpression
     */
    public function create_expression_null()
    {
        $field = 'size';
        $value = new ValueEmpty();

        foreach ([QB_OPERATOR::IS_NULL, QB_OPERATOR::IS_NOT_NULL] as $operator)
        {
            $config = new Config();
            $result = $config->createExpression($field, $operator, $value);
            $this->assertInstanceOf(Expression::class, $result);
            $this->assertEquals($field, $result->getField());
            $this->assertEquals($operator, $result->getOperator());
            $this->assertFalse($result->isParam1());
            $this->assertFalse($result->isParam2());
        }
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::createExpression
     */
    public function create_expression_like()
    {
        $field = 'size';
        $fieldParam = $field.'_cond';

        foreach ([QB_OPERATOR::BEGINS_WITH,
                  QB_OPERATOR::NOT_BEGINS_WITH,
                  QB_OPERATOR::CONTAINS,
                  QB_OPERATOR::NOT_CONTAINS,
                  QB_OPERATOR::ENDS_WITH,
                  QB_OPERATOR::NOT_ENDS_WITH] as $operator)
        {
            $value = 'test';
            $config = new Config();
            $result = $config->createExpression($field, $operator, $value);
            $this->assertInstanceOf(Expression::class, $result);
            $this->assertEquals($field, $result->getField());
            $this->assertEquals($operator, $result->getOperator());
            $this->assertEquals(':'.$fieldParam, $result->getParam1());
            $value = match($operator) {
                QB_OPERATOR::BEGINS_WITH => 'test%',
                QB_OPERATOR::NOT_BEGINS_WITH => 'test%',
                QB_OPERATOR::CONTAINS => '%test%',
                QB_OPERATOR::NOT_CONTAINS => '%test%',
                QB_OPERATOR::ENDS_WITH => '%test',
                QB_OPERATOR::NOT_ENDS_WITH => '%test'
            };
            $this->assertEquals($value, $config->getParameterValue($fieldParam), $operator->name());
            $this->assertFalse($result->isParam2());
        }
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::registerJoin
     */
    public function register_join()
    {
        $attribute = [
            _ATTR::ID->column() => 2,
            _ATTR::NAME->column() => 'size',
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
        ];

        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['addJoin'])->getMock();

        $config->addAttribute($attribute);

        $config->expects($this->once())
            ->method('addJoin')
            ->with(ATTR_TYPE::INTEGER->valueTable(), 'size', 2);

        $config->registerJoin('size');
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::registerJoin
     */
    public function registerJoin_skip()
    {
        $field = 'size';
        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['addJoin', 'hasJoin'])->getMock();
        $config->expects($this->never())->method('addJoin');
        $config->expects($this->once())->method('hasJoin')
            ->with($field)->willReturn(true);
        $config->registerJoin($field);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::registerJoin
     */
    public function registerJoin_attribute_type_exception()
    {
        $field = 'size';
        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['hasJoin'])->getMock();
        $config->addAttribute([
            _ATTR::NAME->column() => 'size',
            _ATTR::TYPE->column() => 'test'
        ]);
        $config->expects($this->once())->method('hasJoin')
            ->with($field)->willReturn(false);
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNSUPPORTED_TYPE, 'test'));
        $config->registerJoin($field);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::registerSelect
     */
    public function registerSelect()
    {
        $field = 'size';
        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['addSelect'])->getMock();
        $config->expects($this->once())->method('addSelect')
            ->with($field);
        $config->registerSelect($field);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::registerSelect
     */
    public function registerSelect_skip()
    {
        $field = 'size';
        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['addSelect', 'isSelected'])->getMock();
        $config->expects($this->never())->method('addSelect');
        $config->expects($this->once())->method('isSelected')
            ->with($field)->willReturn(true);
        $config->registerSelect($field);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::parse
     */
    public function parse_empty()
    {
        $this->config->parse([]);
        $this->assertEquals([], $this->config->getExpressions());
        $this->assertEquals([], $this->config->getAttributes());
        $this->assertEquals([], $this->config->getParameters());
        $this->assertEquals([], $this->config->getExpressions());
        $this->assertEquals([], $this->config->getColumns());
        $this->assertEquals([], $this->config->getSelected());
    }

    /**
     * @test
     *
     * @group functional
     * @covers \Drobotik\Eav\QueryBuilder\Config::parse
     * @covers \Drobotik\Eav\QueryBuilder\Config::getExpressions
     */
    public function parse()
    {
        $attr1Value = 10000;
        $attr2Value = 'sit quisquam';

        $config = [
            QB_CONFIG::CONDITION => QB_CONDITION::AND->name(),
            QB_CONFIG::RULES => [
                [
                    QB_CONFIG::NAME => ATTR_TYPE::DECIMAL->value(),
                    QB_CONFIG::OPERATOR => QB_OPERATOR::LESS->name(),
                    QB_CONFIG::VALUE => 10000
                ],
                [
                    QB_CONFIG::CONDITION => QB_CONDITION::OR->name(),
                    QB_CONFIG::RULES => [
                        [
                            QB_CONFIG::NAME => ATTR_TYPE::STRING->value(),
                            QB_CONFIG::OPERATOR => QB_OPERATOR::CONTAINS->name(),
                            QB_CONFIG::VALUE => 'sit quisquam'
                        ]
                    ],
                ]
            ],
        ];

        $attr0 = [
            _ATTR::ID->column() => 3,
            _ATTR::NAME->column() => ATTR_TYPE::INTEGER->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
        ];

        $attr1 = [
            _ATTR::ID->column() => 4,
            _ATTR::NAME->column() => ATTR_TYPE::DECIMAL->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
        ];

        $attr2 = [
            _ATTR::ID->column() => 5,
            _ATTR::NAME->column() => ATTR_TYPE::STRING->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ];

        $this->config->addAttribute($attr0);
        $this->config->addAttribute($attr1);
        $this->config->addAttribute($attr2);
        $this->config->addColumns([ATTR_TYPE::INTEGER->value()]);
        $this->config->handleColumns();
        $this->config->parse($config);

        $attr1Param = ATTR_TYPE::DECIMAL->value().'_cond';
        $attr2Param = ATTR_TYPE::STRING->value().'_cond';

        $expression1 = new Expression();
        $expression1->setField(ATTR_TYPE::DECIMAL->value());
        $expression1->setOperator(QB_OPERATOR::LESS);
        $expression1->setParam1($attr1Param);
        $expression2 = new Expression();
        $expression2->setField(ATTR_TYPE::STRING->value());
        $expression2->setOperator(QB_OPERATOR::CONTAINS);
        $expression2->setParam1($attr2Param);

        $expressions = [
            QB_CONDITION::AND,
            $expression1,
            [
                QB_CONDITION::OR,
                $expression2
            ]
        ];

        $attributes = [
            $attr0[_ATTR::NAME->column()] => $attr0,
            $attr1[_ATTR::NAME->column()] => $attr1,
            $attr2[_ATTR::NAME->column()] => $attr2
        ];

        $parameters = [
            $attr1Param => $attr1Value,
            $attr2Param => '%'.$attr2Value.'%',
            ATTR_TYPE::INTEGER->value().'_join_'.QB_JOIN::ATTR_PARAM => 3,
            ATTR_TYPE::DECIMAL->value().'_join_'.QB_JOIN::ATTR_PARAM => 4,
            ATTR_TYPE::STRING->value().'_join_'.QB_JOIN::ATTR_PARAM => 5
        ];

        $columns = [
            ATTR_TYPE::INTEGER->value()
        ];

        $selected = [
            ATTR_TYPE::INTEGER->value(),
            ATTR_TYPE::DECIMAL->value(),
            ATTR_TYPE::STRING->value()
        ];

        $this->assertEquals($expressions, $this->config->getExpressions());
        $this->assertEquals($attributes, $this->config->getAttributes());
        $this->assertEquals($parameters, $this->config->getParameters());
        $this->assertEquals($columns, $this->config->getColumns());
        $this->assertEquals($selected, $this->config->getSelected());
    }
}