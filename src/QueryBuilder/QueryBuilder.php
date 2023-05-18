<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\QueryBuilder;

use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

class QueryBuilder
{
    public function select(Builder $query, string $name) : Builder
    {
        return $query->addSelect(sprintf('%1$s.%2$s as %1$s', $name, _VALUE::VALUE->column()));
    }

    public function join(Builder $query, $table, $name, $attributeKey): Builder
    {
        return $query
            ->join(sprintf('%s as %s', $table, $name), function(JoinClause $j) use($name, $attributeKey) {
                $j->on(
                    sprintf('e.%s', _ENTITY::ID->column()),
                    '=',
                    sprintf('%s.%s', $name, _VALUE::ENTITY_ID->column())
                )->where(
                    sprintf('%s.%s', $name, _VALUE::ATTRIBUTE_ID->column()),
                    '=',
                    $attributeKey
                );
            });
    }

    public function condition(
        Builder $query,
        string $name,
        QB_OPERATOR $operator,
        QB_CONDITION $condition,
        mixed $value = null
    )
    : Builder
    {
        $condition = $condition->sql();
        if ($operator->isNeedsArray()) {
            if ($operator == QB_OPERATOR::IN)
            {
                return $query->whereIn($name, $value, $condition);
            }
            else if ($operator == QB_OPERATOR::NOT_IN)
            {
                return $query->whereNotIn($name, $value, $condition);
            }
            else if($operator == QB_OPERATOR::BETWEEN)
            {
                return $query->whereBetween($name, $value, $condition);
            }
            else if($operator == QB_OPERATOR::NOT_BETWEEN)
            {
                return $query->whereNotBetween($name, $value, $condition);
            }
        }
        else if($operator->isNull())
        {
            if ($operator == QB_OPERATOR::IS_NULL)
            {
                return $query->whereNull($name, $condition);
            }
            else if($operator == QB_OPERATOR::IS_NOT_NULL)
            {
                return $query->whereNotNull($name, $condition);
            }
        }
        $value = match($operator) {
            QB_OPERATOR::BEGINS_WITH, QB_OPERATOR::NOT_BEGINS_WITH => '%'.$value,
            QB_OPERATOR::CONTAINS, QB_OPERATOR::NOT_CONTAINS => '%'.$value.'%',
            QB_OPERATOR::ENDS_WITH, QB_OPERATOR::NOT_ENDS_WITH => $value.'%',
            default => $value
        };
        return $query->where($name, $operator->sql(), $value, $condition);
    }

}