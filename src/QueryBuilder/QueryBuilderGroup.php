<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\QueryBuilder;

use Drobotik\Eav\Enum\QB_CONDITION;
use Illuminate\Database\Query\Builder;

class QueryBuilderGroup
{
    private ?QueryBuilderGroup     $parent = null;
    private QueryBuilderAttributes $attributes;
    private QB_CONDITION           $condition;

    /** @var QueryBuilderRule[]|QueryBuilderGroup[] */
    private array $items = [];

    public function __construct() {
        $this->resetCondition();
    }

    public function setParent(QueryBuilderGroup $parent): void
    {
        $this->parent = $parent;
    }

    public function getParent() : QueryBuilderGroup
    {
        return $this->parent;
    }

    public function getAttributes() : QueryBuilderAttributes
    {
        return $this->attributes;
    }

    public function setAttributes(QueryBuilderAttributes $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getCondition(): QB_CONDITION
    {
        return $this->condition;
    }

    public function setCondition(QB_CONDITION $condition): void
    {
        $this->condition = $condition;
    }

    public function resetCondition(): void
    {
        $this->condition = QB_CONDITION::AND;
    }

    public function appendRule(QueryBuilderRule $rule): void
    {
        $rule->setGroup($this);
        $this->items[] = $rule;
    }

    public function appendGroup(QueryBuilderGroup $group): void
    {
        $group->setParent($this);
        $this->items[] = $group;
    }

    public function getItems() : array
    {
        return $this->items;
    }

    public function makeJoins(Builder $query) {
        foreach($this->getItems() as $item) {
            if($item instanceof QueryBuilderGroup) {
                $query = $item->makeJoins($query);
            } else if($item instanceof QueryBuilderRule) {
                $query = $item->join($query);
            }
        }
        return $query;
    }

    public function makeConditions(Builder $query) : Builder
    {
        foreach($this->getItems() as $item) {
            if($item instanceof QueryBuilderGroup) {
                $query = $query->whereNested(fn($q) => $item->makeConditions($q), $item->getParent()->getCondition()->sql());
            } else if($item instanceof QueryBuilderRule) {
                $query = $item->condition($query);
            }
        }
        return $query;
    }
}