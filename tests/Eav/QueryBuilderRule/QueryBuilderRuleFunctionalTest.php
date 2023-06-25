<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderRule;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\QueryBuilder\QueryBuilderGroup;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use Drobotik\Eav\QueryBuilder\QueryBuilder;
use Drobotik\Eav\QueryBuilder\QueryBuilderRule;
use Tests\QueryBuilderTestCase;

class QueryBuilderRuleFunctionalTest extends QueryBuilderTestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::setGroup
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::getGroup
     */
    public function group()
    {
        $group = new QueryBuilderGroup();
        $this->rule->setGroup($group);
        $this->assertSame($group, $this->rule->getGroup());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::makeQueryBuilder
     */
    public function make_query_builder()
    {
        $this->assertInstanceOf(QueryBuilder::class, $this->rule->makeQueryBuilder());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::setName
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::getName
     */
    public function name()
    {
        $this->rule->setName('test');
        $this->assertSame('test' , $this->rule->getName());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::setValue
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::getValue
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::isValue
     */
    public function value()
    {
        $this->assertFalse($this->rule->isValue());
        $this->rule->setValue('test');
        $this->assertTrue($this->rule->isValue());
        $this->assertSame('test' , $this->rule->getValue());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::getValue
     */
    public function value_allow_null()
    {
        $this->assertNull($this->rule->getValue(true));
        $this->rule->setValue('test');
        $this->assertEquals('test', $this->rule->getValue(true));
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::setOperator
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::getOperator
     */
    public function operator()
    {
        $operator = QB_OPERATOR::LESS;
        $this->rule->setOperator($operator);
        $this->assertSame($operator , $this->rule->getOperator());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::condition
     */
    public function condition()
    {
        $attributes = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['isAttribute'])
            ->getMock();
        $attributes->method('isAttribute')->willReturn(true);
        $group = new QueryBuilderGroup();
        $group->setAttributes($attributes);
        $this->rule->setGroup($group);
        $this->rule->setName('name');
        $this->rule->setOperator(QB_OPERATOR::EQUAL);
        $this->rule->setValue('Ben');
        $query = $this->rule->condition($this->getQuery());
        $this->assertEquals('select * where `name`.`value` = ?', $query->toSql());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::condition
     */
    public function condition_null()
    {
        $attributes = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['isAttribute'])
            ->getMock();
        $attributes->method('isAttribute')->willReturn(true);
        $group = new QueryBuilderGroup();
        $group->setAttributes($attributes);
        $this->rule->setGroup($group);
        $this->rule->setName('name');
        $this->rule->setOperator(QB_OPERATOR::EQUAL);
        $query = $this->rule->condition($this->getQuery());
        $this->assertEquals('select * where `name`.`value` is null', $query->toSql());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::condition
     */
    public function condition_not_allowed()
    {
        $attributes = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['isAttribute'])
            ->getMock();
        $attributes->expects($this->once())->method('isAttribute')
            ->with('test')
            ->willReturn(false);

        $group = $this->getMockBuilder(QueryBuilderGroup::class)
            ->onlyMethods(['getAttributes'])
            ->getMock();
        $group->expects($this->once())->method('getAttributes')
            ->willReturn($attributes);

        $rule = $this->getMockBuilder(QueryBuilderRule::class)
            ->onlyMethods(['getName', 'getGroup'])
            ->getMock();
        $rule->expects($this->once())->method('getName')
            ->willReturn('test');
        $rule->expects($this->once())->method('getGroup')
            ->willReturn($group);
        $query = $this->getQuery();
        $result = $rule->condition($query);
        $this->assertEquals($query->toSql(), $result->toSql());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::getAttributeModel
     */
    public function get_attribute_model()
    {
        $group = new QueryBuilderGroup();
        $attributes = new QueryBuilderAttributes();
        $attribute1 = [_ATTR::NAME->column() => 'test1'];
        $attribute2 = [_ATTR::NAME->column() => 'test2'];
        $attributes->appendAttribute($attribute1);
        $attributes->appendAttribute($attribute2);
        $group->setAttributes($attributes);
        $this->rule->setGroup($group);
        $this->rule->setName('test2');
        $this->assertSame($attribute2, $this->rule->getAttributeModel());
        $this->rule->setName('test1');
        $this->assertSame($attribute1, $this->rule->getAttributeModel());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::makeJoin
     */
    public function make_join()
    {
        $group = new QueryBuilderGroup();
        $attributes = new QueryBuilderAttributes();
        $attribute = [
            _ATTR::ID->column() => 32,
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
            _ATTR::NAME->column() => 'size'
        ];
        $attributes->appendAttribute($attribute);
        $group->setAttributes($attributes);
        $this->rule->setGroup($group);
        $this->rule->setName('size');
        $this->rule->setOperator(QB_OPERATOR::LESS);
        $this->rule->setValue(32);
        $expected = (new QueryBuilder())->join(
            $this->getQuery(),
            ATTR_TYPE::INTEGER->valueTable(),
            'size',
            32
        );
        $this->assertEquals($expected->toSql(), $this->rule->makeJoin($this->getQuery())->toSql());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::join
     */
    public function join_not_allowed()
    {
        $attributes = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['isAttribute'])
            ->getMock();
        $attributes->expects($this->once())->method('isAttribute')
            ->with('test')
            ->willReturn(false);

        $group = $this->getMockBuilder(QueryBuilderGroup::class)
            ->onlyMethods(['getAttributes'])
            ->getMock();
        $group->expects($this->once())->method('getAttributes')
            ->willReturn($attributes);

        $rule = $this->getMockBuilder(QueryBuilderRule::class)
            ->onlyMethods(['getName', 'getGroup'])
            ->getMock();
        $rule->expects($this->once())->method('getName')
            ->willReturn('test');
        $rule->expects($this->once())->method('getGroup')
            ->willReturn($group);
        $query = $this->getQuery();
        $result = $rule->join($query);
        $this->assertEquals($query->toSql(), $result->toSql());
    }

}
