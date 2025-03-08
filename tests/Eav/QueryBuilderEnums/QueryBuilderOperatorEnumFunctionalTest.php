<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderEnums;

use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\Exception\QueryBuilderException;
use PHPUnit\Framework\TestCase;

class QueryBuilderOperatorEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::EQUAL
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::NOT_EQUAL
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::IN
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::NOT_IN
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::LESS
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::LESS_OR_EQUAL
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::GREATER
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::GREATER_OR_EQUAL
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::BETWEEN
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::NOT_BETWEEN
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::BEGINS_WITH
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::NOT_BEGINS_WITH
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::CONTAINS
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::NOT_CONTAINS
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::ENDS_WITH
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::NOT_ENDS_WITH
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::IS_EMPTY
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::IS_NOT_EMPTY
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::IS_NULL
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::IS_NOT_NULL
     */
    public function name()
    {
        $this->assertEquals('equal', QB_OPERATOR::EQUAL);
        $this->assertEquals('not_equal', QB_OPERATOR::NOT_EQUAL);
        $this->assertEquals('in', QB_OPERATOR::IN);
        $this->assertEquals('not_in', QB_OPERATOR::NOT_IN);
        $this->assertEquals('less', QB_OPERATOR::LESS);
        $this->assertEquals('less_or_equal', QB_OPERATOR::LESS_OR_EQUAL);
        $this->assertEquals('greater', QB_OPERATOR::GREATER);
        $this->assertEquals('greater_or_equal', QB_OPERATOR::GREATER_OR_EQUAL);
        $this->assertEquals('between', QB_OPERATOR::BETWEEN);
        $this->assertEquals('not_between', QB_OPERATOR::NOT_BETWEEN);
        $this->assertEquals('begins_with', QB_OPERATOR::BEGINS_WITH);
        $this->assertEquals('not_begins_with', QB_OPERATOR::NOT_BEGINS_WITH);
        $this->assertEquals('contains', QB_OPERATOR::CONTAINS);
        $this->assertEquals('not_contains', QB_OPERATOR::NOT_CONTAINS);
        $this->assertEquals('ends_with', QB_OPERATOR::ENDS_WITH);
        $this->assertEquals('not_ends_with', QB_OPERATOR::NOT_ENDS_WITH);
        $this->assertEquals('is_empty', QB_OPERATOR::IS_EMPTY);
        $this->assertEquals('is_not_empty', QB_OPERATOR::IS_NOT_EMPTY);
        $this->assertEquals('is_null', QB_OPERATOR::IS_NULL);
        $this->assertEquals('is_not_null', QB_OPERATOR::IS_NOT_NULL);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::getCase
     */
    public function get_case()
    {
        $this->assertEquals(QB_OPERATOR::EQUAL, QB_OPERATOR::getCase(QB_OPERATOR::EQUAL));
        $this->assertEquals(QB_OPERATOR::NOT_EQUAL, QB_OPERATOR::getCase(QB_OPERATOR::NOT_EQUAL));
        $this->assertEquals(QB_OPERATOR::IN, QB_OPERATOR::getCase(QB_OPERATOR::IN));
        $this->assertEquals(QB_OPERATOR::NOT_IN, QB_OPERATOR::getCase(QB_OPERATOR::NOT_IN));
        $this->assertEquals(QB_OPERATOR::LESS, QB_OPERATOR::getCase(QB_OPERATOR::LESS));
        $this->assertEquals(QB_OPERATOR::LESS_OR_EQUAL, QB_OPERATOR::getCase(QB_OPERATOR::LESS_OR_EQUAL));
        $this->assertEquals(QB_OPERATOR::GREATER, QB_OPERATOR::getCase(QB_OPERATOR::GREATER));
        $this->assertEquals(QB_OPERATOR::GREATER_OR_EQUAL, QB_OPERATOR::getCase(QB_OPERATOR::GREATER_OR_EQUAL));
        $this->assertEquals(QB_OPERATOR::BETWEEN, QB_OPERATOR::getCase(QB_OPERATOR::BETWEEN));
        $this->assertEquals(QB_OPERATOR::NOT_BETWEEN, QB_OPERATOR::getCase(QB_OPERATOR::NOT_BETWEEN));
        $this->assertEquals(QB_OPERATOR::BEGINS_WITH, QB_OPERATOR::getCase(QB_OPERATOR::BEGINS_WITH));
        $this->assertEquals(QB_OPERATOR::NOT_BEGINS_WITH, QB_OPERATOR::getCase(QB_OPERATOR::NOT_BEGINS_WITH));
        $this->assertEquals(QB_OPERATOR::CONTAINS, QB_OPERATOR::getCase(QB_OPERATOR::CONTAINS));
        $this->assertEquals(QB_OPERATOR::NOT_CONTAINS, QB_OPERATOR::getCase(QB_OPERATOR::NOT_CONTAINS));
        $this->assertEquals(QB_OPERATOR::ENDS_WITH, QB_OPERATOR::getCase(QB_OPERATOR::ENDS_WITH));
        $this->assertEquals(QB_OPERATOR::NOT_ENDS_WITH, QB_OPERATOR::getCase(QB_OPERATOR::NOT_ENDS_WITH));
        $this->assertEquals(QB_OPERATOR::IS_EMPTY, QB_OPERATOR::getCase(QB_OPERATOR::IS_EMPTY));
        $this->assertEquals(QB_OPERATOR::IS_NOT_EMPTY, QB_OPERATOR::getCase(QB_OPERATOR::IS_NOT_EMPTY));
        $this->assertEquals(QB_OPERATOR::IS_NULL, QB_OPERATOR::getCase(QB_OPERATOR::IS_NULL));
        $this->assertEquals(QB_OPERATOR::IS_NOT_NULL, QB_OPERATOR::getCase(QB_OPERATOR::IS_NOT_NULL));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::getCase
     */
    public function get_unsupported_case()
    {
        $this->expectException(QueryBuilderException::class);
        $slug = "test";
        $message = sprintf(QueryBuilderException::UNSUPPORTED_OPERATOR, $slug);
        $this->expectExceptionMessage($message);
        QB_OPERATOR::getCase($slug);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isValueRequired
     */
    public function is_value_required()
    {
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::EQUAL));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::NOT_EQUAL));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::IN));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::NOT_IN));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::LESS));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::LESS_OR_EQUAL));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::GREATER));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::GREATER_OR_EQUAL));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::BETWEEN));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::NOT_BETWEEN));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::BEGINS_WITH));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::NOT_BEGINS_WITH));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::CONTAINS));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::NOT_CONTAINS));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::ENDS_WITH));
        $this->assertTrue(QB_OPERATOR::isValueRequired(QB_OPERATOR::NOT_ENDS_WITH));
        $this->assertFalse(QB_OPERATOR::isValueRequired(QB_OPERATOR::IS_EMPTY));
        $this->assertFalse(QB_OPERATOR::isValueRequired(QB_OPERATOR::IS_NOT_EMPTY));
        $this->assertFalse(QB_OPERATOR::isValueRequired(QB_OPERATOR::IS_NULL));
        $this->assertFalse(QB_OPERATOR::isValueRequired(QB_OPERATOR::IS_NOT_NULL));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::applyTo
     */
    public function apply_to()
    {
        $expected = [
            ATTR_TYPE::INTEGER,
            ATTR_TYPE::DECIMAL,
            ATTR_TYPE::DATETIME,
        ];
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::EQUAL));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::NOT_EQUAL));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::IN));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::NOT_IN));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::LESS));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::LESS_OR_EQUAL));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::GREATER));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::GREATER_OR_EQUAL));

        $expected = [
            ATTR_TYPE::STRING,
            ATTR_TYPE::TEXT,
        ];
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::BEGINS_WITH));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::NOT_BEGINS_WITH));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::CONTAINS));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::NOT_CONTAINS));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::ENDS_WITH));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::NOT_ENDS_WITH));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::IS_EMPTY));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::IS_NOT_EMPTY));

        $expected = [
            ATTR_TYPE::STRING,
            ATTR_TYPE::TEXT,
            ATTR_TYPE::INTEGER,
            ATTR_TYPE::DECIMAL,
            ATTR_TYPE::DATETIME,
        ];
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::IS_NULL));
        $this->assertEquals($expected, QB_OPERATOR::applyTo(QB_OPERATOR::IS_NOT_NULL));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::sql
     */
    public function sql()
    {
        $this->assertSame('=', QB_OPERATOR::sql(QB_OPERATOR::EQUAL));
        $this->assertSame('!=', QB_OPERATOR::sql(QB_OPERATOR::NOT_EQUAL));
        $this->assertSame('IN', QB_OPERATOR::sql(QB_OPERATOR::IN));
        $this->assertSame('NOT IN', QB_OPERATOR::sql(QB_OPERATOR::NOT_IN));
        $this->assertSame('<', QB_OPERATOR::sql(QB_OPERATOR::LESS));
        $this->assertSame('<=', QB_OPERATOR::sql(QB_OPERATOR::LESS_OR_EQUAL));
        $this->assertSame('>', QB_OPERATOR::sql(QB_OPERATOR::GREATER));
        $this->assertSame('>=', QB_OPERATOR::sql(QB_OPERATOR::GREATER_OR_EQUAL));
        $this->assertSame('BETWEEN', QB_OPERATOR::sql(QB_OPERATOR::BETWEEN));
        $this->assertSame('NOT BETWEEN', QB_OPERATOR::sql(QB_OPERATOR::NOT_BETWEEN));
        $this->assertSame('LIKE', QB_OPERATOR::sql(QB_OPERATOR::BEGINS_WITH));
        $this->assertSame('LIKE', QB_OPERATOR::sql(QB_OPERATOR::CONTAINS));
        $this->assertSame('LIKE', QB_OPERATOR::sql(QB_OPERATOR::ENDS_WITH));
        $this->assertSame('NOT LIKE', QB_OPERATOR::sql(QB_OPERATOR::NOT_BEGINS_WITH));
        $this->assertSame('NOT LIKE', QB_OPERATOR::sql(QB_OPERATOR::NOT_ENDS_WITH));
        $this->assertSame('NOT LIKE', QB_OPERATOR::sql(QB_OPERATOR::NOT_CONTAINS));
        $this->assertSame('NULL', QB_OPERATOR::sql(QB_OPERATOR::IS_NULL));
        $this->assertSame('NOT NULL', QB_OPERATOR::sql(QB_OPERATOR::IS_NOT_NULL));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::prepend
     */
    public function prepend()
    {
        $this->assertSame('%', QB_OPERATOR::prepend(QB_OPERATOR::ENDS_WITH));
        $this->assertSame('%', QB_OPERATOR::prepend(QB_OPERATOR::NOT_ENDS_WITH));
        $this->assertSame('%', QB_OPERATOR::prepend(QB_OPERATOR::NOT_CONTAINS));
        $this->assertSame('%', QB_OPERATOR::prepend(QB_OPERATOR::CONTAINS));
        $this->assertFalse(QB_OPERATOR::prepend(QB_OPERATOR::EQUAL));
        $this->assertFalse(QB_OPERATOR::prepend(QB_OPERATOR::IS_NULL));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::append
     */
    public function append()
    {
        $this->assertSame('%', QB_OPERATOR::append(QB_OPERATOR::BEGINS_WITH));
        $this->assertSame('%', QB_OPERATOR::append(QB_OPERATOR::NOT_BEGINS_WITH));
        $this->assertSame('%', QB_OPERATOR::append(QB_OPERATOR::CONTAINS));
        $this->assertSame('%', QB_OPERATOR::append(QB_OPERATOR::NOT_CONTAINS));
        $this->assertFalse(QB_OPERATOR::append(QB_OPERATOR::EQUAL));
        $this->assertFalse(QB_OPERATOR::append(QB_OPERATOR::IS_NULL));
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isNeedsArray
     */
    public function is_needs_array()
    {
        $this->assertTrue(QB_OPERATOR::isNeedsArray(QB_OPERATOR::NOT_IN));
        $this->assertTrue(QB_OPERATOR::isNeedsArray(QB_OPERATOR::IN));
        $this->assertTrue(QB_OPERATOR::isNeedsArray(QB_OPERATOR::NOT_BETWEEN));
        $this->assertTrue(QB_OPERATOR::isNeedsArray(QB_OPERATOR::BETWEEN));
        $this->assertFalse(QB_OPERATOR::isNeedsArray(QB_OPERATOR::EQUAL));
        $this->assertFalse(QB_OPERATOR::isNeedsArray(QB_OPERATOR::IS_NULL));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isNull
     */
    public function is_null()
    {
        $this->assertTrue(QB_OPERATOR::isNull(QB_OPERATOR::IS_NOT_NULL));
        $this->assertTrue(QB_OPERATOR::isNull(QB_OPERATOR::IS_NULL));
        $this->assertFalse(QB_OPERATOR::isNull(QB_OPERATOR::EQUAL));
        $this->assertFalse(QB_OPERATOR::isNull(QB_OPERATOR::IS_EMPTY));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isEmpty
     */
    public function is_empty()
    {
        $this->assertTrue(QB_OPERATOR::isEmpty(QB_OPERATOR::IS_EMPTY));
        $this->assertTrue(QB_OPERATOR::isEmpty(QB_OPERATOR::IS_NOT_EMPTY));
        $this->assertFalse(QB_OPERATOR::isEmpty(QB_OPERATOR::EQUAL));
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isBetween
     */
    public function is_between()
    {
        $this->assertTrue(QB_OPERATOR::isBetween(QB_OPERATOR::BETWEEN));
        $this->assertTrue(QB_OPERATOR::isBetween(QB_OPERATOR::NOT_BETWEEN));
        $this->assertFalse(QB_OPERATOR::isBetween(QB_OPERATOR::EQUAL));
        $this->assertFalse(QB_OPERATOR::isBetween(QB_OPERATOR::IS_EMPTY));
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::expr
     */
    public function expr()
    {
        $this->assertEquals('eq', QB_OPERATOR::expr(QB_OPERATOR::EQUAL));
        $this->assertEquals('neq', QB_OPERATOR::expr(QB_OPERATOR::NOT_EQUAL));
        $this->assertEquals('in', QB_OPERATOR::expr(QB_OPERATOR::IN));
        $this->assertEquals('notIn', QB_OPERATOR::expr(QB_OPERATOR::NOT_IN));
        $this->assertEquals('lt', QB_OPERATOR::expr(QB_OPERATOR::LESS));
        $this->assertEquals('lte', QB_OPERATOR::expr(QB_OPERATOR::LESS_OR_EQUAL));
        $this->assertEquals('gt', QB_OPERATOR::expr(QB_OPERATOR::GREATER));
        $this->assertEquals('gte', QB_OPERATOR::expr(QB_OPERATOR::GREATER_OR_EQUAL));
        $this->assertEquals('between', QB_OPERATOR::expr(QB_OPERATOR::BETWEEN));
        $this->assertEquals('notBetween', QB_OPERATOR::expr(QB_OPERATOR::NOT_BETWEEN));
        $this->assertEquals('like', QB_OPERATOR::expr(QB_OPERATOR::BEGINS_WITH));
        $this->assertEquals('notLike', QB_OPERATOR::expr(QB_OPERATOR::NOT_BEGINS_WITH));
        $this->assertEquals('like', QB_OPERATOR::expr(QB_OPERATOR::CONTAINS));
        $this->assertEquals('notLike', QB_OPERATOR::expr(QB_OPERATOR::NOT_CONTAINS));
        $this->assertEquals('like', QB_OPERATOR::expr(QB_OPERATOR::ENDS_WITH));
        $this->assertEquals('notLike', QB_OPERATOR::expr(QB_OPERATOR::NOT_ENDS_WITH));
        $this->assertEquals('eq', QB_OPERATOR::expr(QB_OPERATOR::IS_EMPTY));
        $this->assertEquals('neq', QB_OPERATOR::expr(QB_OPERATOR::IS_NOT_EMPTY));
        $this->assertEquals('isNull', QB_OPERATOR::expr(QB_OPERATOR::IS_NULL));
        $this->assertEquals('isNotNull', QB_OPERATOR::expr(QB_OPERATOR::IS_NOT_NULL));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isLike
     */
    public function isLike()
    {
        $this->assertTrue(QB_OPERATOR::isLike(QB_OPERATOR::BEGINS_WITH));
        $this->assertTrue(QB_OPERATOR::isLike(QB_OPERATOR::NOT_BEGINS_WITH));
        $this->assertTrue(QB_OPERATOR::isLike(QB_OPERATOR::CONTAINS));
        $this->assertTrue(QB_OPERATOR::isLike(QB_OPERATOR::NOT_CONTAINS));
        $this->assertTrue(QB_OPERATOR::isLike(QB_OPERATOR::ENDS_WITH));
        $this->assertTrue(QB_OPERATOR::isLike(QB_OPERATOR::NOT_ENDS_WITH));
    }
}
