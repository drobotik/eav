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
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_CONFIG;
use Drobotik\Eav\Enum\QB_JOIN;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\Exception\QueryBuilderException;
use Drobotik\Eav\Value\ValueEmpty;

class Config
{
    // :domainKey
    private string $domainKeyParam;
    // :setKey
    private string $setKeyParam;
    // joins which will be performed
    private array $joins = [];
    // eav attributes array for internal usage to not allow wrong fields to query
    private array $attributes = [];
    // result columns which should be visible on grid
    private array $columns = [];
    // fields that need to be in the SELECT statement
    private array $selected = [];
    // query bind parameters with values
    private array $parameters = [];
    // query expressions recursive array
    /** @var Expression[] $expressions */
    private array $expressions = [];

    public function getDomainKeyParam() : string
    {
        return $this->domainKeyParam;
    }

    public function setDomainKey(int $key) : void
    {
        $paramName = $this->createParameter('domainKey', $key);
        $this->domainKeyParam = $paramName;
    }

    public function getSetKeyParam() : string
    {
        return $this->setKeyParam;
    }

    public function setSetKey(int $key) : void
    {
        $paramName = $this->createParameter('setKey', $key);
        $this->setKeyParam = $paramName;
    }

    public function hasJoin(string $name) : bool
    {
        return key_exists($name, $this->joins);
    }

    public function addJoin(string $table, string $name, int $attrKey) : void
    {
        $paramName = $this->createParameter(
            sprintf('%s_join_%s', $name, QB_JOIN::ATTR_PARAM), $attrKey);

        $this->joins[$name] = [
            QB_JOIN::NAME => $name,
            QB_JOIN::TABLE => $table,
            QB_JOIN::ATTR_PARAM => $paramName
        ];
    }

    public function getJoins() : array
    {
        return $this->joins;
    }

    public function getJoin(string $name) : array
    {
        return $this->joins[$name];
    }

    public function addColumns(array $columns) : void
    {
        $this->columns = array_values(array_unique($columns));
    }

    public function getColumns() : array
    {
        return $this->columns;
    }

    public function addAttributes(array $attributes) : void
    {
        foreach ($attributes as $attribute)
            $this->addAttribute($attribute);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addAttribute(array $attribute): void
    {
        $this->attributes[$attribute[_ATTR::NAME]] = $attribute;
    }

    public function getAttribute(string $name) : array
    {
        return $this->attributes[$name];
    }

    public function hasAttribute(string $name) : bool
    {
        return key_exists($name, $this->attributes);
    }

    public function addSelect(string $field) : void
    {
        $this->selected[] = $field;
    }

    public function getSelected() : array
    {
        $selected = $this->selected;
        $columns = $this->getColumns();
        usort($selected, function ($a, $b) use ( $columns) {
            $indexA = array_search($a, $columns);
            $indexB = array_search($b, $columns);
            return $indexA - $indexB;
        });
        $selected = array_unique($selected);
        return array_merge(array_intersect($columns, $selected), array_diff($selected, $columns));
    }
    public function isSelected(string $field) : bool
    {
        return in_array($field, $this->selected);
    }

    public function hasParameter(string $name): bool
    {
        return key_exists($name, $this->parameters);
    }

    public function getParameterValue(string $name) : mixed
    {
        return $this->parameters[$name];
    }

    public function createParameter(string $name, mixed $value) : string
    {
        if($this->hasParameter($name)) {
            $uniqueName = sprintf('%s_%s', $name, substr(md5(uniqid()), 0, 5));
            return $this->createParameter($uniqueName, $value);
        } else {
            $this->parameters[$name] = $value;
        }
        return $name;
    }

    public function getParameters() : array
    {
        return $this->parameters;
    }

    public function getExpressions() : array
    {
        return $this->expressions;
    }

    public function handleColumns(): void
    {
        foreach($this->getColumns() as $column)
        {
            if (!$this->hasAttribute($column)) {
                QueryBuilderException::unsupportedAttribute($column);
            }

            $attribute = $this->getAttribute($column);
            $attributeKey = $attribute[_ATTR::ID];
            $table = ATTR_TYPE::getCase($attribute[_ATTR::TYPE])->valueTable();

            // if in search it should be joined
            if(!$this->hasJoin($column)) {
                $this->addJoin($table, $column, $attributeKey);
                if(!$this->isSelected($column)) {
                    $this->addSelect($column);
                }
            }
        }
    }

    public function handleGroup(array $group) : array
    {
        $result = [];
        $result[] = QB_CONDITION::getCase($group[QB_CONFIG::CONDITION]);
        $rules = $group[QB_CONFIG::RULES];
        foreach($rules as $rule)
        {
            if (key_exists(QB_CONFIG::CONDITION, $rule)) {
                $result[] = $this->handleGroup($rule);
            }
            else {
                $result[] = $this->handleRule($rule);
            }
        }
        return $result;
    }

    public function createExpression(string $field, QB_OPERATOR $operator, mixed $value) : Expression
    {
        $expression = new Expression();
        $expression->setField($field);
        $expression->setOperator($operator);

        if($operator->isBetween()) {
            $value1 = $value[0];
            $value2 = $value[1];
            $param1 = $this->createParameter($field .'_cond_1', $value1);
            $param2 = $this->createParameter($field .'_cond_2', $value2);
            $expression->setParam1($param1);
            $expression->setParam2($param2);

        } else if($operator->isEmpty()) {
            $param = $this->createParameter($field .'_cond', '');
            $expression->setParam1($param);

        } else if(!$operator->isNull()) {
            if($operator->isLike()) {
                $value = match($operator) {
                    QB_OPERATOR::BEGINS_WITH, QB_OPERATOR::NOT_BEGINS_WITH => $value.'%',
                    QB_OPERATOR::CONTAINS, QB_OPERATOR::NOT_CONTAINS => '%'.$value.'%',
                    QB_OPERATOR::ENDS_WITH ,QB_OPERATOR::NOT_ENDS_WITH => '%'.$value
                };

            }
            $param = $this->createParameter($field .'_cond', $value);
            $expression->setParam1($param);
        }

        return $expression;
    }

    public function registerSelect(string $field) : void
    {
        if(!$this->isSelected($field)) {
            $this->addSelect($field);
        }
    }

    public function registerJoin(string $field) : void
    {
        if(!$this->hasJoin($field)) {
            $attribute = $this->getAttribute($field);
            $table = ATTR_TYPE::getCase($attribute[_ATTR::TYPE])->valueTable();
            $attributeKey = $attribute[_ATTR::ID];
            $this->addJoin($table, $field, $attributeKey);
        }
    }

    public function handleRule(array $rule) : Expression
    {
        $field = $rule[QB_CONFIG::NAME];
        $operator = QB_OPERATOR::getCase($rule[QB_CONFIG::OPERATOR]);
        $value = key_exists(QB_CONFIG::VALUE, $rule)
            ? $rule[QB_CONFIG::VALUE]
            : new ValueEmpty();

        $this->registerSelect($field);
        $this->registerJoin($field);

        return $this->createExpression($field, $operator, $value);
    }

    public function parse(array $config) : void
    {
        if(empty($config))
            return;

        $this->expressions = $this->handleGroup($config);
    }

}