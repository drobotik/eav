<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderRule;

use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\QueryBuilder\QueryBuilderGroup;
use Drobotik\Eav\QueryBuilder\QueryBuilder;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use Drobotik\Eav\QueryBuilder\QueryBuilderRule;
use Tests\QueryBuilderTestCase;

class QueryBuilderRuleBehaviorTest extends QueryBuilderTestCase
{
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::condition
     */
    public function condition()
    {
        $query = $this->getQuery();
        $name = 'name';
        $operator = QB_OPERATOR::LESS;
        $condition = QB_CONDITION::OR;
        $value = 'value';

        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->onlyMethods(['condition'])
            ->getMock();
        $queryBuilder->expects($this->once())->method('condition')
            ->with(
                $query,
                sprintf('%s.%s', $name, _VALUE::VALUE->column()),
                $operator,
                $condition,
                $value
            );

        $attributes = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['isAttribute'])
            ->getMock();

        $attributes->expects($this->once())->method('isAttribute')
            ->with($name)
            ->willReturn(true);

        $group = $this->getMockBuilder(QueryBuilderGroup::class)
            ->onlyMethods(['getCondition', 'getAttributes'])
            ->getMock();
        $group->expects($this->once())->method('getCondition')->willReturn($condition);
        $group->expects($this->once())->method('getAttributes')->willReturn($attributes);

        $rule = $this->getMockBuilder(QueryBuilderRule::class)
            ->onlyMethods([
                'makeQueryBuilder',
                'getName',
                'getOperator',
                'getGroup',
                'isValue',
                'getValue'
            ])
            ->getMock();

        $rule->expects($this->once())->method('getGroup')->willReturn($group);
        $rule->expects($this->once())->method('makeQueryBuilder')->willReturn($queryBuilder);
        $rule->expects($this->once())->method('getName')->willReturn($name);
        $rule->expects($this->once())->method('getOperator')->willReturn($operator);
        $rule->expects($this->once())->method('getValue')
            ->with(true)
            ->willReturn($value);

        $rule->condition($query);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::join
     */
    public function join()
    {
        $attributes = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['isAttributeJoined', 'setAttributeJoined', 'isAttribute'])
            ->getMock();
        $attributes->expects($this->once())->method('isAttribute')
            ->with('test')
            ->willReturn(true);
        $attributes->expects($this->once())->method('isAttributeJoined')
            ->with('test')
            ->willReturn(false);
        $attributes ->expects($this->once())->method('setAttributeJoined')
            ->with('test');

        $group = new QueryBuilderGroup();
        $group->setAttributes($attributes);

        $rule = $this->getMockBuilder(QueryBuilderRule::class)
            ->onlyMethods(['getGroup', 'makeJoin', 'getName'])
            ->getMock();

        $rule->expects($this->exactly(1))->method('getName')
            ->willReturn('test');
        $rule->expects($this->exactly(1))->method('getGroup')
            ->willReturn($group);

        $oldQuery = $this->getQuery();
        $newQuery = $this->getQuery();

        $rule->expects($this->once())->method('makeJoin')
            ->with($oldQuery)
            ->willReturn($newQuery);

        $result = $rule->join($oldQuery);
        $this->assertSame($newQuery, $result);
    }
}