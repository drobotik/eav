<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\QueryBuilder;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_JOIN;
use Drobotik\Eav\Exception\QueryBuilderException;
use Doctrine\DBAL\Query\QueryBuilder as Query;


class QueryBuilder
{
    private Config $config;
    private Query $query;

    public function __construct()
    {
        $this->query = Connection::get()->createQueryBuilder();
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setConfig(Config $config) : void
    {
        $this->config = $config;
    }

    public function getConfig() : Config
    {
        return $this->config;
    }

    public function startQuery(): void
    {
        $this->query->from(_ENTITY::table(), 'e');
        $this->query->addSelect('e.'._ENTITY::ID->column());
        $config = $this->getConfig();
        foreach($config->getSelected() as $selected) {
            $this->select($selected);
        }
        foreach ($config->getJoins() as $join) {
            $this->join($join[QB_JOIN::NAME]);
        }
    }

    public function endQuery(): void
    {
        $config = $this->getConfig();
        $query = $this->getQuery();
        $query->andwhere(sprintf('e.%s = :%s', _ENTITY::DOMAIN_ID->column(), $config->getDomainKeyParam()));
        $query->andWhere(sprintf('e.%s = :%s', _ENTITY::ATTR_SET_ID->column(), $config->getSetKeyParam()));
    }


    public function run() : Query
    {
        $this->startQuery();
        $this->applyExpressions($this->getConfig()->getExpressions(), QB_CONDITION::AND);
        $this->endQuery();
        $this->applyParams();
        return $this->getQuery();
    }

    /**
     * @throws QueryBuilderException
     */
    public function applyExpressions(array $expressions, ?QB_CONDITION $condition = null) : void
    {
        $query = $this->getQuery();
        $expr = $query->expr();
        $executed = [];
        foreach ($expressions as $expression) {
            if ($expression instanceof QB_CONDITION) {
                $condition = $expression;
            } else if ($expression instanceof Expression) {
                $executed[] = $expression->execute();
            } elseif (is_array($expression)) {
                $subOperator = $expression[0] ?? throw new QueryBuilderException('must have condition');
                $ee = array_slice($expression, 1);
                $this->applyExpressions($ee, $subOperator);
            }
        }

        if (count($executed) > 0) {
            if ($condition === QB_CONDITION::AND) {
                $query->andWhere($expr->and(...$executed));
            } elseif ($condition === QB_CONDITION::OR) {
                $query->orWhere($expr->or(...$executed));
            }
        }
    }

    public function applyParams(): void
    {
        $parameters = $this->getConfig()->getParameters();
        $this->getQuery()->setParameters($parameters);
    }

    public function select(string $name) : Query
    {
        return $this->getQuery()->addSelect(sprintf('%1$s.%2$s as %1$s', $name, _VALUE::VALUE->column()));
    }

    public function join(string $name): Query
    {
        $config = $this->getConfig();
        [
            QB_JOIN::NAME => $name,
            QB_JOIN::TABLE => $table,
            QB_JOIN::ATTR_PARAM => $paramName
        ] = $config->getJoin($name);
        return
            $this->getQuery()->innerJoin('e', $table, $name,
                sprintf('e.%s = %s.%s AND %s.%s = :%s',
                    _ENTITY::ID->column(),
                    $name,
                    _VALUE::ENTITY_ID->column(),
                    $name,
                    _VALUE::ATTRIBUTE_ID->column(),
                    $paramName
                )
            );
    }
}