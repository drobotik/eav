<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderGroup;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_OPERATOR;
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
        $nameAttr = [
            _ATTR::NAME->column() => 'name',
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ];
        $locationAttr = [
            _ATTR::NAME->column() => 'location',
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ];
        $thingAttr =[
            _ATTR::NAME->column() => 'thing',
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ];

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
        $nameAttr = [
            _ATTR::ID->column() => 10,
            _ATTR::NAME->column() => 'name',
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ];

        $locationAttr = [
            _ATTR::ID->column() => 11,
            _ATTR::NAME->column() => 'location',
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ];

        $codeAttr = [
            _ATTR::ID->column() => 12,
            _ATTR::NAME->column() => 'code',
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
        ];

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