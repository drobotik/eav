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
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Trait\QueryBuilderSingletons;
use Drobotik\Eav\Trait\RepositoryTrait;
use Drobotik\Eav\Trait\SingletonsTrait;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar as MySQLGrammar;
use Illuminate\Database\Query\Processors\MySqlProcessor as MySQLProcessor;

class QueryBuilderManager
{
    use QueryBuilderSingletons;
    use RepositoryTrait;
    use SingletonsTrait;

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
        return new Builder(Manager::connection(), new MySQLGrammar(), new MySQLProcessor());
    }

    public function makeQuery() : Builder
    {
        $query = $this->getBuilder();
        $query->from(_ENTITY::table(), 'e');
        $query->where('e.'._ENTITY::DOMAIN_ID->column(), '=', $this->getDomainKey());
        $query->where('e.'._ENTITY::ATTR_SET_ID->column(), '=', $this->getSetKey());
        $query->addSelect('e.'._ENTITY::ID->column());
        return $query;
    }

    public function markSelected(array $attribute, Builder $query) : Builder
    {
        $attributes = $this->getAttributesPivot();
        $queryBuilder = $this->makeQueryBuilder();
        $name = $attribute[_ATTR::NAME->column()];
        $attributes->setAttributeSelected($name);
        return $queryBuilder->select($query, $name);
    }

    public function markJoined(array $attribute, Builder $query) : Builder
    {
        $attributes = $this->getAttributesPivot();
        $queryBuilder = $this->makeQueryBuilder();
        $key = $attribute[_ATTR::ID->column()];
        $name = $attribute[_ATTR::NAME->column()];
        $type = ATTR_TYPE::getCase($attribute[_ATTR::TYPE->column()]);
        $attributes->setAttributeJoined($name);
        return $queryBuilder->join($query, $type->valueTable(), $name, $key);
    }

    public function setupAttribute(array $attribute, Builder $query) : Builder
    {
        $name = $attribute[_ATTR::NAME->column()];
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
        $model = $this->makeAttributeSetModel();
        $linkedAttrs = $model->findAttributes($this->getDomainKey(), $this->getSetKey());
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