<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderQuery;

use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\QueryBuilder\QueryBuilder;
use Illuminate\Database\Connection as Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar as MySQLGrammar;
use Illuminate\Database\Query\Processors\MySqlProcessor as MySQLProcessor;
use PHPUnit\Framework\TestCase;

class QueryBuilderQueryFunctionalTest extends TestCase
{
    private QueryBuilder $q;

    public function setUp(): void
    {
        parent::setUp();
        $this->q = new QueryBuilder();
    }

    private function getQuery() : Builder
    {
        $pdo = new \PDO('sqlite::memory:');
        return new Builder(new Connection($pdo), new MySQLGrammar(), new MySQLProcessor());
    }


    private function checkOperator(QB_OPERATOR $operator)
    {
        $condition = QB_CONDITION::AND;
        $query = $this->q->condition($this->getQuery(), 'name', $operator, $condition, 'Ben');
        $operator = $operator->sql();
        $this->assertEquals(
            sprintf('select * where `name` %s ?', $operator),
            $query->toSql(),
            $operator
        );
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::condition
     */
    public function condition_base_operators()
    {
        $this->checkOperator(QB_OPERATOR::EQUAL);
        $this->checkOperator(QB_OPERATOR::IS_EMPTY);
        $this->checkOperator(QB_OPERATOR::NOT_EQUAL);
        $this->checkOperator(QB_OPERATOR::IS_NOT_EMPTY);
        $this->checkOperator(QB_OPERATOR::LESS);
        $this->checkOperator(QB_OPERATOR::LESS_OR_EQUAL);
        $this->checkOperator(QB_OPERATOR::GREATER);
        $this->checkOperator(QB_OPERATOR::GREATER_OR_EQUAL);
        $this->checkOperator(QB_OPERATOR::BEGINS_WITH);
        $this->checkOperator(QB_OPERATOR::CONTAINS);
        $this->checkOperator(QB_OPERATOR::ENDS_WITH);
        $this->checkOperator(QB_OPERATOR::NOT_BEGINS_WITH);
        $this->checkOperator(QB_OPERATOR::NOT_ENDS_WITH);
        $this->checkOperator(QB_OPERATOR::NOT_CONTAINS);
    }

    private function checkValue(QB_OPERATOR $operator, $expected)
    {
        $condition = QB_CONDITION::AND;
        $query = $this->q->condition($this->getQuery(), 'name', $operator, $condition, 'Ben');
        $this->assertEquals(
            [$expected],
            $query->getBindings(),
            $operator->sql()
        );
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::condition
     */
    public function condition_where_like_values()
    {
        $this->checkValue(QB_OPERATOR::BEGINS_WITH, '%Ben');
        $this->checkValue(QB_OPERATOR::NOT_BEGINS_WITH, '%Ben');
        $this->checkValue(QB_OPERATOR::CONTAINS, '%Ben%');
        $this->checkValue(QB_OPERATOR::NOT_CONTAINS, '%Ben%');
        $this->checkValue(QB_OPERATOR::ENDS_WITH, 'Ben%');
        $this->checkValue(QB_OPERATOR::NOT_ENDS_WITH, 'Ben%');
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::condition
     */
    public function condition_where_in()
    {
        $query = $this->q->condition(
            $this->getQuery(),
            'name',
            QB_OPERATOR::IN,
            QB_CONDITION::AND,
            ['Tom','Jerry']
        );
        $this->assertEquals('select * where `name` in (?, ?)', $query->toSql());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::condition
     */
    public function condition_where_not_in()
    {
        $query = $this->q->condition(
            $this->getQuery(),
            'name',
            QB_OPERATOR::NOT_IN,
            QB_CONDITION::AND,
            ['Tom','Jerry']
        );
        $this->assertEquals('select * where `name` not in (?, ?)', $query->toSql());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::condition
     */
    public function condition_where_between()
    {
        $query = $this->q->condition(
            $this->getQuery(),
            'name',
            QB_OPERATOR::BETWEEN,
            QB_CONDITION::AND,
            ['Tom','Jerry','Chip']
        );
        $this->assertEquals('select * where `name` between ? and ?', $query->toSql());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::condition
     */
    public function condition_where_not_between()
    {
        $query = $this->q->condition(
            $this->getQuery(),
            'name',
            QB_OPERATOR::NOT_BETWEEN,
            QB_CONDITION::AND,
            ['Tom','Jerry','Chip']
        );
        $this->assertEquals('select * where `name` not between ? and ?', $query->toSql());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::condition
     */
    public function condition_where_null()
    {
        $query = $this->q->condition(
            $this->getQuery(),
            'name',
            QB_OPERATOR::IS_NULL,
            QB_CONDITION::AND
        );
        $this->assertEquals('select * where `name` is null', $query->toSql());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::condition
     */
    public function condition_where_not_null()
    {
        $query = $this->q->condition(
            $this->getQuery(),
            'name',
            QB_OPERATOR::IS_NOT_NULL,
            QB_CONDITION::AND
        );
        $this->assertEquals('select * where `name` is not null', $query->toSql());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::select
     */
    public function select()
    {
        $query = $this->q->select(
            $this->getQuery(),
            'name',
        );
        $this->assertEquals('select `name`.`value` as `name`', $query->toSql());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilder::join
     */
    public function join()
    {
        $table = 'tbl';
        $name = 'price';
        $attrKey = 2;
        $query = $this->q->join($this->getQuery(), $table, $name, $attrKey);
        $format = 'select * inner join `tbl` as `price` on `e`.`%1$s` = `price`.`%2$s` and `price`.`%3$s` = ?';
        $expected = sprintf($format,
            _ENTITY::ID->column(),
            _VALUE::ENTITY_ID->column(),
            _VALUE::ATTRIBUTE_ID->column()
        );
        $this->assertEquals($expected, $query->toSql());
        $this->assertEquals([2], $query->getBindings());
    }
}