<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderGroup;

use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\QueryBuilder\QueryBuilder;
use Drobotik\Eav\QueryBuilder\QueryBuilderGroup;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use Drobotik\Eav\QueryBuilder\QueryBuilderRule;
use Tests\QueryBuilderTestCase;

class QueryBuilderGroupAcceptanceTest extends QueryBuilderTestCase
{
    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::makeConditions
     */
    public function make_conditions()
    {
        $nameAttr = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $nameAttr->setName('name');
        $nameAttr->setType(ATTR_TYPE::STRING->value());

        $locationAttr = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $locationAttr->setName('location');
        $locationAttr->setType(ATTR_TYPE::STRING->value());

        $thingAttr = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $thingAttr->setName('thing');
        $thingAttr->setType(ATTR_TYPE::STRING->value());

        $attributes = new QueryBuilderAttributes();
        $attributes->appendAttribute($nameAttr);
        $attributes->appendAttribute($locationAttr);
        $attributes->appendAttribute($thingAttr);

        $group = new QueryBuilderGroup();
        $group->setAttributes($attributes);
        $group->setCondition(QB_CONDITION::OR);
        $rule = new QueryBuilderRule();
        $rule->setName("name");
        $rule->setOperator(QB_OPERATOR::CONTAINS);
        $rule->setValue("Tom");
        $group->appendRule($rule);
        $rule = new QueryBuilderRule();
        $rule->setName("name");
        $rule->setOperator(QB_OPERATOR::EQUAL);
        $rule->setValue("Jerry");
        $group->appendRule($rule);
        $sub = new QueryBuilderGroup();
        $sub->setAttributes($attributes);
        $rule = new QueryBuilderRule();
        $rule->setName("location");
        $rule->setOperator(QB_OPERATOR::EQUAL);
        $rule->setValue("House");
        $sub->appendRule($rule);
        $subSub = new QueryBuilderGroup();
        $subSub->setAttributes($attributes);
        $subSub->setCondition(QB_CONDITION::OR);
        $rule = new QueryBuilderRule();
        $rule->setName("thing");
        $rule->setOperator(QB_OPERATOR::EQUAL);
        $rule->setValue("table");
        $subSub->appendRule($rule);
        $rule = new QueryBuilderRule();
        $rule->setName("thing");
        $rule->setOperator(QB_OPERATOR::EQUAL);
        $rule->setValue("toy");
        $subSub->appendRule($rule);
        $sub->appendGroup($subSub);
        $group->appendGroup($sub);
        $query = $group->makeConditions($this->getQuery());
        $expected = 'select * where `name`.`value` LIKE ? or `name`.`value` = ? or (`location`.`value` = ? and (`thing`.`value` = ? or `thing`.`value` = ?))';
        $this->assertEquals($expected, $query->toSql());
        $this->assertEquals(['%Tom%', 'Jerry', 'House', 'table', 'toy'], $query->getBindings());
    }
    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::makeJoins
     */
    public function make_joins()
    {
        $nameAttr = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $nameAttr->expects($this->once())->method('getKey')->willReturn(10);
        $nameAttr->setName('name');
        $nameAttr->setType(ATTR_TYPE::STRING->value());

        $locationAttr = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $locationAttr->expects($this->once())->method('getKey')->willReturn(11);
        $locationAttr->setName('location');
        $locationAttr->setType(ATTR_TYPE::STRING->value());

        $codeAttr = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $codeAttr->expects($this->once())->method('getKey')->willReturn(12);
        $codeAttr->setName('code');
        $codeAttr->setType(ATTR_TYPE::INTEGER->value());

        $attributes = new QueryBuilderAttributes();
        $attributes->appendAttribute($nameAttr);
        $attributes->appendAttribute($locationAttr);
        $attributes->appendAttribute($codeAttr);

        $group = new QueryBuilderGroup();
        $group->setAttributes($attributes);

        $group->setCondition(QB_CONDITION::OR);
        $rule = new QueryBuilderRule();
        $rule->setName("name");
        $rule->setOperator(QB_OPERATOR::CONTAINS);
        $rule->setValue("Tom");
        $group->appendRule($rule);
        $rule = new QueryBuilderRule();
        $rule->setName("name");
        $rule->setOperator(QB_OPERATOR::EQUAL);
        $rule->setValue("Jerry");
        $group->appendRule($rule);

        $sub = new QueryBuilderGroup();
        $sub->setAttributes($attributes);
        $rule = new QueryBuilderRule();
        $rule->setName("location");
        $rule->setOperator(QB_OPERATOR::EQUAL);
        $rule->setValue("House");
        $sub->appendRule($rule);
        $rule = new QueryBuilderRule();
        $rule->setName("location");
        $rule->setOperator(QB_OPERATOR::EQUAL);
        $rule->setValue("Lake");
        $sub->appendRule($rule);
        $rule = new QueryBuilderRule();
        $rule->setName("code");
        $rule->setOperator(QB_OPERATOR::EQUAL);
        $rule->setValue(123);
        $sub->appendRule($rule);
        $group->appendGroup($sub);

        $query = $this->getQuery();
        $qb = new QueryBuilder();
        $query = $qb->join(
            $query,
            ATTR_TYPE::STRING->valueTable(),
            'name',
            10
        );
        $query = $qb->join(
            $query,
            ATTR_TYPE::STRING->valueTable(),
            'location',
            11
        );
        $query = $qb->join(
            $query,
            ATTR_TYPE::INTEGER->valueTable(),
            'code',
            12
        );
        $this->assertEquals($query->toSql(), $group->makeJoins($this->getQuery())->toSql());
    }

}