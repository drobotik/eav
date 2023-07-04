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
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::name
     */
    public function name()
    {
        $this->assertEquals('equal', QB_OPERATOR::EQUAL->name());
        $this->assertEquals('not_equal', QB_OPERATOR::NOT_EQUAL->name());
        $this->assertEquals('in', QB_OPERATOR::IN->name());
        $this->assertEquals('not_in', QB_OPERATOR::NOT_IN->name());
        $this->assertEquals('less', QB_OPERATOR::LESS->name());
        $this->assertEquals('less_or_equal', QB_OPERATOR::LESS_OR_EQUAL->name());
        $this->assertEquals('greater', QB_OPERATOR::GREATER->name());
        $this->assertEquals('greater_or_equal', QB_OPERATOR::GREATER_OR_EQUAL->name());
        $this->assertEquals('between', QB_OPERATOR::BETWEEN->name());
        $this->assertEquals('not_between', QB_OPERATOR::NOT_BETWEEN->name());
        $this->assertEquals('begins_with', QB_OPERATOR::BEGINS_WITH->name());
        $this->assertEquals('not_begins_with', QB_OPERATOR::NOT_BEGINS_WITH->name());
        $this->assertEquals('contains', QB_OPERATOR::CONTAINS->name());
        $this->assertEquals('not_contains', QB_OPERATOR::NOT_CONTAINS->name());
        $this->assertEquals('ends_with', QB_OPERATOR::ENDS_WITH->name());
        $this->assertEquals('not_ends_with', QB_OPERATOR::NOT_ENDS_WITH->name());
        $this->assertEquals('is_empty', QB_OPERATOR::IS_EMPTY->name());
        $this->assertEquals('is_not_empty', QB_OPERATOR::IS_NOT_EMPTY->name());
        $this->assertEquals('is_null', QB_OPERATOR::IS_NULL->name());
        $this->assertEquals('is_not_null', QB_OPERATOR::IS_NOT_NULL->name());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::getCase
     */
    public function get_case()
    {
        $this->assertEquals(QB_OPERATOR::EQUAL, QB_OPERATOR::getCase(QB_OPERATOR::EQUAL->name()));
        $this->assertEquals(QB_OPERATOR::NOT_EQUAL, QB_OPERATOR::getCase(QB_OPERATOR::NOT_EQUAL->name()));
        $this->assertEquals(QB_OPERATOR::IN, QB_OPERATOR::getCase(QB_OPERATOR::IN->name()));
        $this->assertEquals(QB_OPERATOR::NOT_IN, QB_OPERATOR::getCase(QB_OPERATOR::NOT_IN->name()));
        $this->assertEquals(QB_OPERATOR::LESS, QB_OPERATOR::getCase(QB_OPERATOR::LESS->name()));
        $this->assertEquals(QB_OPERATOR::LESS_OR_EQUAL, QB_OPERATOR::getCase(QB_OPERATOR::LESS_OR_EQUAL->name()));
        $this->assertEquals(QB_OPERATOR::GREATER, QB_OPERATOR::getCase(QB_OPERATOR::GREATER->name()));
        $this->assertEquals(QB_OPERATOR::GREATER_OR_EQUAL, QB_OPERATOR::getCase(QB_OPERATOR::GREATER_OR_EQUAL->name()));
        $this->assertEquals(QB_OPERATOR::BETWEEN, QB_OPERATOR::getCase(QB_OPERATOR::BETWEEN->name()));
        $this->assertEquals(QB_OPERATOR::NOT_BETWEEN, QB_OPERATOR::getCase(QB_OPERATOR::NOT_BETWEEN->name()));
        $this->assertEquals(QB_OPERATOR::BEGINS_WITH, QB_OPERATOR::getCase(QB_OPERATOR::BEGINS_WITH->name()));
        $this->assertEquals(QB_OPERATOR::NOT_BEGINS_WITH, QB_OPERATOR::getCase(QB_OPERATOR::NOT_BEGINS_WITH->name()));
        $this->assertEquals(QB_OPERATOR::CONTAINS, QB_OPERATOR::getCase(QB_OPERATOR::CONTAINS->name()));
        $this->assertEquals(QB_OPERATOR::NOT_CONTAINS, QB_OPERATOR::getCase(QB_OPERATOR::NOT_CONTAINS->name()));
        $this->assertEquals(QB_OPERATOR::ENDS_WITH, QB_OPERATOR::getCase(QB_OPERATOR::ENDS_WITH->name()));
        $this->assertEquals(QB_OPERATOR::NOT_ENDS_WITH, QB_OPERATOR::getCase(QB_OPERATOR::NOT_ENDS_WITH->name()));
        $this->assertEquals(QB_OPERATOR::IS_EMPTY, QB_OPERATOR::getCase(QB_OPERATOR::IS_EMPTY->name()));
        $this->assertEquals(QB_OPERATOR::IS_NOT_EMPTY, QB_OPERATOR::getCase(QB_OPERATOR::IS_NOT_EMPTY->name()));
        $this->assertEquals(QB_OPERATOR::IS_NULL, QB_OPERATOR::getCase(QB_OPERATOR::IS_NULL->name()));
        $this->assertEquals(QB_OPERATOR::IS_NOT_NULL, QB_OPERATOR::getCase(QB_OPERATOR::IS_NOT_NULL->name()));
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
        $this->assertTrue(QB_OPERATOR::EQUAL->isValueRequired());
        $this->assertTrue(QB_OPERATOR::NOT_EQUAL->isValueRequired());
        $this->assertTrue(QB_OPERATOR::IN->isValueRequired());
        $this->assertTrue(QB_OPERATOR::NOT_IN->isValueRequired());
        $this->assertTrue(QB_OPERATOR::LESS->isValueRequired());
        $this->assertTrue(QB_OPERATOR::LESS_OR_EQUAL->isValueRequired());
        $this->assertTrue(QB_OPERATOR::GREATER->isValueRequired());
        $this->assertTrue(QB_OPERATOR::GREATER_OR_EQUAL->isValueRequired());
        $this->assertTrue(QB_OPERATOR::BETWEEN->isValueRequired());
        $this->assertTrue(QB_OPERATOR::NOT_BETWEEN->isValueRequired());
        $this->assertTrue(QB_OPERATOR::BEGINS_WITH->isValueRequired());
        $this->assertTrue(QB_OPERATOR::NOT_BEGINS_WITH->isValueRequired());
        $this->assertTrue(QB_OPERATOR::CONTAINS->isValueRequired());
        $this->assertTrue(QB_OPERATOR::NOT_CONTAINS->isValueRequired());
        $this->assertTrue(QB_OPERATOR::ENDS_WITH->isValueRequired());
        $this->assertTrue(QB_OPERATOR::NOT_ENDS_WITH->isValueRequired());
        $this->assertFalse(QB_OPERATOR::IS_EMPTY->isValueRequired());
        $this->assertFalse(QB_OPERATOR::IS_NOT_EMPTY->isValueRequired());
        $this->assertFalse(QB_OPERATOR::IS_NULL->isValueRequired());
        $this->assertFalse(QB_OPERATOR::IS_NOT_NULL->isValueRequired());
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
        $this->assertEquals($expected, QB_OPERATOR::EQUAL->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::NOT_EQUAL->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::IN->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::NOT_IN->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::LESS->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::LESS_OR_EQUAL->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::GREATER->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::GREATER_OR_EQUAL->applyTo());

        $expected = [
            ATTR_TYPE::STRING,
            ATTR_TYPE::TEXT,
        ];
        $this->assertEquals($expected, QB_OPERATOR::BEGINS_WITH->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::NOT_BEGINS_WITH->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::CONTAINS->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::NOT_CONTAINS->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::ENDS_WITH->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::NOT_ENDS_WITH->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::IS_EMPTY->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::IS_NOT_EMPTY->applyTo());

        $expected = [
            ATTR_TYPE::STRING,
            ATTR_TYPE::TEXT,
            ATTR_TYPE::INTEGER,
            ATTR_TYPE::DECIMAL,
            ATTR_TYPE::DATETIME,
        ];
        $this->assertEquals($expected, QB_OPERATOR::IS_NULL->applyTo());
        $this->assertEquals($expected, QB_OPERATOR::IS_NOT_NULL->applyTo());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::sql
     */
    public function sql()
    {
        $this->assertSame('=', QB_OPERATOR::EQUAL->sql());
        $this->assertSame('!=', QB_OPERATOR::NOT_EQUAL->sql());
        $this->assertSame('IN', QB_OPERATOR::IN->sql());
        $this->assertSame('NOT IN', QB_OPERATOR::NOT_IN->sql());
        $this->assertSame('<', QB_OPERATOR::LESS->sql());
        $this->assertSame('<=', QB_OPERATOR::LESS_OR_EQUAL->sql());
        $this->assertSame('>', QB_OPERATOR::GREATER->sql());
        $this->assertSame('>=', QB_OPERATOR::GREATER_OR_EQUAL->sql());
        $this->assertSame('BETWEEN', QB_OPERATOR::BETWEEN->sql());
        $this->assertSame('NOT BETWEEN', QB_OPERATOR::NOT_BETWEEN->sql());
        $this->assertSame('LIKE', QB_OPERATOR::BEGINS_WITH->sql());
        $this->assertSame('LIKE', QB_OPERATOR::CONTAINS->sql());
        $this->assertSame('LIKE', QB_OPERATOR::ENDS_WITH->sql());
        $this->assertSame('NOT LIKE', QB_OPERATOR::NOT_BEGINS_WITH->sql());
        $this->assertSame('NOT LIKE', QB_OPERATOR::NOT_ENDS_WITH->sql());
        $this->assertSame('NOT LIKE', QB_OPERATOR::NOT_CONTAINS->sql());
        $this->assertSame('NULL', QB_OPERATOR::IS_NULL->sql());
        $this->assertSame('NOT NULL', QB_OPERATOR::IS_NOT_NULL->sql());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::prepend
     */
    public function prepend()
    {
        $this->assertSame('%', QB_OPERATOR::ENDS_WITH->prepend());
        $this->assertSame('%', QB_OPERATOR::NOT_ENDS_WITH->prepend());
        $this->assertSame('%', QB_OPERATOR::NOT_CONTAINS->prepend());
        $this->assertSame('%', QB_OPERATOR::CONTAINS->prepend());
        $this->assertFalse(QB_OPERATOR::EQUAL->prepend());
        $this->assertFalse(QB_OPERATOR::IS_NULL->prepend());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::append
     */
    public function append()
    {
        $this->assertSame('%', QB_OPERATOR::BEGINS_WITH->append());
        $this->assertSame('%', QB_OPERATOR::NOT_BEGINS_WITH->append());
        $this->assertSame('%', QB_OPERATOR::CONTAINS->append());
        $this->assertSame('%', QB_OPERATOR::NOT_CONTAINS->append());
        $this->assertFalse(QB_OPERATOR::EQUAL->append());
        $this->assertFalse(QB_OPERATOR::IS_NULL->append());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isNeedsArray
     */
    public function is_needs_array()
    {
        $this->assertTrue(QB_OPERATOR::NOT_IN->isNeedsArray());
        $this->assertTrue(QB_OPERATOR::IN->isNeedsArray());
        $this->assertTrue(QB_OPERATOR::NOT_BETWEEN->isNeedsArray());
        $this->assertTrue(QB_OPERATOR::BETWEEN->isNeedsArray());
        $this->assertFalse(QB_OPERATOR::EQUAL->isNeedsArray());
        $this->assertFalse(QB_OPERATOR::IS_NULL->isNeedsArray());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isNull
     */
    public function is_null()
    {
        $this->assertTrue(QB_OPERATOR::IS_NOT_NULL->isNull());
        $this->assertTrue(QB_OPERATOR::IS_NULL->isNull());
        $this->assertFalse(QB_OPERATOR::EQUAL->isNull());
        $this->assertFalse(QB_OPERATOR::IS_EMPTY->isNull());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isEmpty
     */
    public function is_empty()
    {
        $this->assertTrue(QB_OPERATOR::IS_EMPTY->isEmpty());
        $this->assertTrue(QB_OPERATOR::IS_NOT_EMPTY->isEmpty());
        $this->assertFalse(QB_OPERATOR::EQUAL->isEmpty());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isBetween
     */
    public function is_between()
    {
        $this->assertTrue(QB_OPERATOR::BETWEEN->isBetween());
        $this->assertTrue(QB_OPERATOR::NOT_BETWEEN->isBetween());
        $this->assertFalse(QB_OPERATOR::EQUAL->isBetween());
        $this->assertFalse(QB_OPERATOR::IS_EMPTY->isBetween());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::expr
     */
    public function expr()
    {
        $this->assertEquals('eq', QB_OPERATOR::EQUAL->expr());
        $this->assertEquals('neq', QB_OPERATOR::NOT_EQUAL->expr());
        $this->assertEquals('in', QB_OPERATOR::IN->expr());
        $this->assertEquals('notIn', QB_OPERATOR::NOT_IN->expr());
        $this->assertEquals('lt', QB_OPERATOR::LESS->expr());
        $this->assertEquals('lte', QB_OPERATOR::LESS_OR_EQUAL->expr());
        $this->assertEquals('gt', QB_OPERATOR::GREATER->expr());
        $this->assertEquals('gte', QB_OPERATOR::GREATER_OR_EQUAL->expr());
        $this->assertEquals('between', QB_OPERATOR::BETWEEN->expr());
        $this->assertEquals('notBetween', QB_OPERATOR::NOT_BETWEEN->expr());
        $this->assertEquals('like', QB_OPERATOR::BEGINS_WITH->expr());
        $this->assertEquals('notLike', QB_OPERATOR::NOT_BEGINS_WITH->expr());
        $this->assertEquals('like', QB_OPERATOR::CONTAINS->expr());
        $this->assertEquals('notLike', QB_OPERATOR::NOT_CONTAINS->expr());
        $this->assertEquals('like', QB_OPERATOR::ENDS_WITH->expr());
        $this->assertEquals('notLike', QB_OPERATOR::NOT_ENDS_WITH->expr());
        $this->assertEquals('eq', QB_OPERATOR::IS_EMPTY->expr());
        $this->assertEquals('neq', QB_OPERATOR::IS_NOT_EMPTY->expr());
        $this->assertEquals('isNull', QB_OPERATOR::IS_NULL->expr());
        $this->assertEquals('isNotNull', QB_OPERATOR::IS_NOT_NULL->expr());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_OPERATOR::isLike
     */
    public function isLike()
    {
        $this->assertTrue(QB_OPERATOR::BEGINS_WITH->isLike());
        $this->assertTrue(QB_OPERATOR::NOT_BEGINS_WITH->isLike());
        $this->assertTrue(QB_OPERATOR::CONTAINS->isLike());
        $this->assertTrue(QB_OPERATOR::NOT_CONTAINS->isLike());
        $this->assertTrue(QB_OPERATOR::ENDS_WITH->isLike());
        $this->assertTrue(QB_OPERATOR::NOT_ENDS_WITH->isLike());
    }
}
