<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilder;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\QueryBuilder\Expression;
use Tests\QueryingDataTestCase;

class QueryBuilderExpressionFunctionalTest extends QueryingDataTestCase
{
    private Expression $expression;

    public function setUp(): void
    {
        parent::setUp();
        $this->expression = new Expression();
        $this->expression->setField('test');
        $this->expression->setParam1('param1');
        $this->expression->setParam2('param2');
        $this->expression->setOperator(QB_OPERATOR::EQUAL);
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Expression::setField
     * @covers \Drobotik\Eav\QueryBuilder\Expression::getField
     */
    public function name()
    {
        $this->assertEquals('test', $this->expression->getField());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Expression::setOperator
     * @covers \Drobotik\Eav\QueryBuilder\Expression::getOperator
     */
    public function operator()
    {
        $this->assertEquals(QB_OPERATOR::EQUAL, $this->expression->getOperator());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Expression::setParam1
     * @covers \Drobotik\Eav\QueryBuilder\Expression::getParam1
     */
    public function param1()
    {
        $this->assertEquals(':param1', $this->expression->getParam1());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Expression::setParam2
     * @covers \Drobotik\Eav\QueryBuilder\Expression::getParam2
     */
    public function param2()
    {
        $this->assertEquals(':param2', $this->expression->getParam2());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\Expression::execute
     */
    public function execute()
    {
        $this->assertEquals('test = :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::NOT_EQUAL);
        $this->assertEquals('test <> :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::IN);
        $this->assertEquals('test IN (:param1)', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::NOT_IN);
        $this->assertEquals('test NOT IN (:param1)', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::LESS);
        $this->assertEquals('test < :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::LESS_OR_EQUAL);
        $this->assertEquals('test <= :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::GREATER);
        $this->assertEquals('test > :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::GREATER_OR_EQUAL);
        $this->assertEquals('test >= :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::BETWEEN);
        $q = Connection::get()->createQueryBuilder();
        $expr = $this->expression->execute();
        $this->assertInstanceOf(CompositeExpression::class, $expr);
        $this->assertEquals('SELECT  WHERE (test >= :param1) AND (test <= :param2)', $q->where($expr)->getSQL());
        $this->expression->setOperator(QB_OPERATOR::NOT_BETWEEN);
        $q = Connection::get()->createQueryBuilder();
        $expr = $this->expression->execute();
        $this->assertInstanceOf(CompositeExpression::class, $expr);
        $this->assertEquals('SELECT  WHERE (test < :param1) OR (test > :param2)', $q->where($expr)->getSQL());
        $this->expression->setOperator(QB_OPERATOR::CONTAINS);
        $this->assertEquals('test LIKE :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::ENDS_WITH);
        $this->assertEquals('test LIKE :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::BEGINS_WITH);
        $this->assertEquals('test LIKE :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::NOT_CONTAINS);
        $this->assertEquals('test NOT LIKE :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::NOT_ENDS_WITH);
        $this->assertEquals('test NOT LIKE :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::NOT_BEGINS_WITH);
        $this->assertEquals('test NOT LIKE :param1', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::IS_EMPTY);
        $this->assertEquals('test IS NULL', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::IS_NOT_NULL);
        $this->assertEquals('test IS NOT NULL', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::IS_NOT_EMPTY);
        $this->assertEquals('test IS NOT NULL', $this->expression->execute());
        $this->expression->setOperator(QB_OPERATOR::IS_NULL);
        $this->assertEquals('test IS NULL', $this->expression->execute());
    }

}