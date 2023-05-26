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
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Trait\QueryBuilderSingletons;
use Drobotik\Eav\Trait\RepositoryTrait;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar as MySQLGrammar;
use Illuminate\Database\Query\Processors\MySqlProcessor as MySQLProcessor;

class QueryBuilderManager
{
    use QueryBuilderSingletons;
    use RepositoryTrait;

    private int $domainKey;
    private int $setKey;
    private array $filters;
    private array $columns;
    private QueryBuilderAttributes $attributes;


    public function getDomainKey() : int
    {
        return $this->domainKey;
    }

    public function setDomainKey(int $key) : void
    {
        $this->domainKey = $key;
    }

    public function getSetKey() : int
    {
        return $this->setKey;
    }

    public function setSetKey(int $key) : void
    {
        $this->setKey = $key;
    }

    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    public function getColumns() : array
    {
        return $this->columns;
    }

    public function isColumns() : bool
    {
        return isset($this->columns) && !empty($this->columns);
    }

    public function isColumn(string $column) : bool
    {
        if(!$this->isColumns()) return false;
        return in_array($column, $this->getColumns());
    }

    public function isManualColumn(string $column) : bool
    {
        return isset($this->columns) && !empty($this->columns) && in_array($column, $this->getColumns());
    }


    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    public function getFilters() : array
    {
        return $this->filters;
    }

    public function setAttributesPivot(QueryBuilderAttributes $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttributesPivot() : QueryBuilderAttributes
    {
        return $this->attributes;
    }

    public function getBuilder() : Builder
    {
        $capsule = new Manager();
        return new Builder($capsule->getConnection(), new MySQLGrammar(), new MySQLProcessor());
    }

    public function makeQuery() : Builder
    {
        $query = $this->getBuilder();
        $query->from(_ENTITY::table(), 'e');
        $query->where(_ENTITY::DOMAIN_ID->column(), '=', $this->getDomainKey());
        $query->where(_ENTITY::ATTR_SET_ID->column(), '=', $this->getSetKey());
        $query->addSelect(_ENTITY::ID->column());
        return $query;
    }

    public function markSelected(AttributeModel $attribute, Builder $query) : Builder
    {
        $attributes = $this->getAttributesPivot();
        $queryBuilder = $this->makeQueryBuilder();
        $name = $attribute->getName();
        $attributes->setAttributeSelected($name);
        return $queryBuilder->select($query, $name);
    }

    public function markJoined(AttributeModel $attribute, Builder $query) : Builder
    {
        $attributes = $this->getAttributesPivot();
        $queryBuilder = $this->makeQueryBuilder();
        $key = $attribute->getKey();
        $name = $attribute->getName();
        $type = $attribute->getTypeEnum();
        $attributes->setAttributeJoined($name);
        return $queryBuilder->join($query, $type->valueTable(), $name, $key);
    }

    public function setupAttribute(AttributeModel $attribute, Builder $query) : Builder
    {
        $name = $attribute->getName();
        if(!$this->isManualColumn($name))
        {
            return $query;
        }
        $query = $this->markSelected($attribute, $query);
        return $this->markJoined($attribute, $query);
    }

    public function makeAttributes(Builder $query) : Builder
    {
        foreach($this->getAttributesPivot()->getAttributes() as $attribute)
        {
            $query = $this->setupAttribute($attribute, $query);
        }
        return $query;
    }

    public function initialize() : Builder
    {
        $attributes = $this->makeQueryBuilderAttributes();
        $repository = $this->makeAttributeRepository();
        $linkedAttrs = $repository->getLinked($this->getDomainKey(), $this->getSetKey());
        $attributes->setAttributes($linkedAttrs);
        $this->setAttributesPivot($attributes);
        $query = $this->makeQuery();
        return $this->makeAttributes($query);
    }

    public function run() : Builder
    {
        $query = $this->initialize();
        $parser = $this->makeQueryBuilderParser();
        $parser->setManager($this);
        $group = $parser->parse($this->getFilters());
        $group->makeJoins($query);
        return $group->makeConditions($query);
    }
}