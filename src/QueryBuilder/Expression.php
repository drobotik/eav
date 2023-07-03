<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\QueryBuilder;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\QB_OPERATOR;

class Expression
{
    private QB_OPERATOR $operator;
    private string      $field;
    private string      $param1;
    private string $param2;

    public function setOperator(QB_OPERATOR $operator): void
    {
        $this->operator = $operator;
    }

    public function getOperator() : QB_OPERATOR
    {
        return $this->operator;
    }

    public function setField(string $name): void
    {
        $this->field = $name;
    }

    public function getField() : string
    {
        return $this->field;
    }

    public function setParam1(string $name): void
    {
        $this->param1 = $name;
    }

    public function getParam1() : string
    {
        return ':'.$this->param1;
    }

    public function setParam2(string $name): void
    {
        $this->param2 = $name;
    }

    public function getParam2() : string
    {
        return ':'.$this->param2;
    }


    public function execute() : string|CompositeExpression
    {
        $expr = new ExpressionBuilder(Connection::get());

        switch ($this->getOperator()) {
            case QB_OPERATOR::EQUAL:

                return $expr->eq($this->getField(), $this->getParam1());

            case QB_OPERATOR::NOT_EQUAL:

                return $expr->neq($this->getField(), $this->getParam1());

            case QB_OPERATOR::IN:

                return $expr->in($this->getField(), $this->getParam1());

            case QB_OPERATOR::NOT_IN:

                return $expr->notIn($this->getField(), $this->getParam1());

            case QB_OPERATOR::LESS:

                return $expr->lt($this->getField(), $this->getParam1());

            case QB_OPERATOR::LESS_OR_EQUAL:

                return $expr->lte($this->getField(), $this->getParam1());

            case QB_OPERATOR::GREATER:

                return $expr->gt($this->getField(), $this->getParam1());

            case QB_OPERATOR::GREATER_OR_EQUAL:

                return $expr->gte($this->getField(), $this->getParam1());

            case QB_OPERATOR::BETWEEN:

                $field = $this->getField();
                return new CompositeExpression(CompositeExpression::TYPE_AND, [
                    $expr->gte($field, $this->getParam1()),
                    $expr->lte($field, $this->getParam2())
                ]);

            case QB_OPERATOR::NOT_BETWEEN:

                $field = $this->getField();
                return new CompositeExpression(CompositeExpression::TYPE_OR, [
                    $expr->lt($field, $this->getParam1()),
                    $expr->gt($field, $this->getParam2())
                ]);

            case QB_OPERATOR::CONTAINS:
            case QB_OPERATOR::ENDS_WITH:
            case QB_OPERATOR::BEGINS_WITH:

                return $expr->like($this->getField(), $this->getParam1());

            case QB_OPERATOR::NOT_CONTAINS:
            case QB_OPERATOR::NOT_ENDS_WITH:
            case QB_OPERATOR::NOT_BEGINS_WITH:

                return $expr->notLike($this->getField(), $this->getParam1());

            case QB_OPERATOR::IS_NULL:
            case QB_OPERATOR::IS_EMPTY:

                return $expr->isNull($this->getField());

            case QB_OPERATOR::IS_NOT_NULL:
            case QB_OPERATOR::IS_NOT_EMPTY:

                return $expr->isNotNull($this->getField());

        }

        return '';
    }
}