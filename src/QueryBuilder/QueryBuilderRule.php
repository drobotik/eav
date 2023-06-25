<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\QueryBuilder;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\Trait\QueryBuilderSingletons;
use Illuminate\Database\Query\Builder;

class QueryBuilderRule
{
    use QueryBuilderSingletons;

    private string $name;
    private mixed $value;
    private QB_OPERATOR $operator;
    private QueryBuilderGroup $group;

    public function getGroup() : QueryBuilderGroup
    {
        return $this->group;
    }

    public function setGroup(QueryBuilderGroup $group): void
    {
        $this->group = $group;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function setValue($value): void
    {
        $this->value = $value;
    }

    public function getValue($allowNull = false) : mixed
    {
        if($allowNull) {
            return $this->isValue()
                ? $this->getValue()
                : null;
        }
        return $this->value;
    }

    public function isValue() : bool
    {
        return isset($this->value);
    }

    public function setOperator(QB_OPERATOR $operator) : void
    {
        $this->operator = $operator;
    }

    public function getOperator() : QB_OPERATOR
    {
        return $this->operator;
    }

    public function getAttributeModel() : array
    {
        $name = $this->getName();
        $group = $this->getGroup();
        $attributes = $group->getAttributes();
        return $attributes->getAttribute($name);
    }

    public function makeJoin(Builder $query) : Builder
    {
        $attribute = $this->getAttributeModel();
        $attributeKey = $attribute[_ATTR::ID->column()];
        $type = ATTR_TYPE::getCase($attribute[_ATTR::TYPE->column()]);
        $table = $type->valueTable();
        return $this->makeQueryBuilder()->join($query, $table, $this->getName(), $attributeKey);
    }

    public function join(Builder $query): Builder
    {
        $name = $this->getName();
        $attributes = $this->getGroup()->getAttributes();

        if(!$attributes->isAttribute($name))
        {
            return $query;
        }

        if(!$attributes->isAttributeJoined($name))
        {
            $query = $this->makeJoin($query);
            $attributes->setAttributeJoined($name);
        }

        return $query;
    }

    public function condition(Builder $query): Builder
    {
        $name = $this->getName();
        $group = $this->getGroup();
        $attributes = $group->getAttributes();

        if(!$attributes->isAttribute($name))
        {
            return $query;
        }

        return $this->makeQueryBuilder()->condition(
            $query,
            sprintf('%s.%s', $name, _VALUE::VALUE->column()),
            $this->getOperator(),
            $group->getCondition(),
            $this->getValue(true)
        );
    }

}