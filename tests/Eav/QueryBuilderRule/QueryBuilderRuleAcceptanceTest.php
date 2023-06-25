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
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\QueryBuilder\QueryBuilderGroup;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use Drobotik\Eav\QueryBuilder\QueryBuilderRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar as MySQLGrammar;
use Illuminate\Database\Query\Processors\MySqlProcessor as MySQLProcessor;
use Tests\TestCase;

class QueryBuilderRuleAcceptanceTest extends TestCase
{

    protected function getQuery() : Builder
    {
        return new Builder(
            $this->capsule->getConnection(),
            new MySQLGrammar(),
            new MySQLProcessor()
        );
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderRule::join
     */
    public function make_join()
    {
        $domainKey = $this->eavFactory->createDomain();
        $attributeKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::NAME->column() => 'price',
            _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
        ]);
        $attributes = new QueryBuilderAttributes();
        $attributes->appendAttribute([
            _ATTR::ID->column() => $attributeKey,
            _ATTR::NAME->column() => 'price',
            _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
        ]);

        $group = new QueryBuilderGroup();
        $group->setAttributes($attributes);
        $rule = new QueryBuilderRule();
        $rule->setGroup($group);
        $rule->setName('price');
        $rule->setOperator(QB_OPERATOR::EQUAL);
        $query = $rule->join($this->getQuery());
        $format = 'select * inner join `%s` as `price` on `e`.`%s` = `price`.`%s` and `price`.`%s` = ?';
        $expected = sprintf($format,
            ATTR_TYPE::DECIMAL->valueTable(),
            _ENTITY::ID->column(),
            _VALUE::ENTITY_ID->column(),
            _VALUE::ATTRIBUTE_ID->column()
        );
        $this->assertEquals($expected, $query->toSql());
        $this->assertEquals([1], $query->getBindings());
        $this->assertTrue($attributes->isAttributeJoined('price'));
    }
}