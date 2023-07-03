<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilder;

use PHPUnit\Framework\TestCase;
use Drobotik\Eav\Exception\QueryBuilderException;

class QueryBuilderExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\QueryBuilderException::unsupportedCondition
     */
    public function unsupportedCondition()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage(sprintf(QueryBuilderException::UNSUPPORTED_CONDITION, 'test'));
        QueryBuilderException::unsupportedCondition('test');
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\QueryBuilderException::unsupportedAttribute
     */
    public function unsupportedAttribute()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage(sprintf(QueryBuilderException::UNSUPPORTED_ATTRIBUTE, 'test'));
        QueryBuilderException::unsupportedAttribute('test');
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\QueryBuilderException::unsupportedOperator
     */
    public function unsupportedOperator()
    {
        $this->expectException(QueryBuilderException::class);
        $this->expectExceptionMessage(sprintf(QueryBuilderException::UNSUPPORTED_OPERATOR, 'test'));
        QueryBuilderException::unsupportedOperator('test');
    }
}