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

    private ExpressionBuilder $exprBuilder;

    public function __construct()
    {
        $this->exprBuilder = new ExpressionBuilder(Connection::get());
    }

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

    public function isParam1() : bool
    {
        return isset($this->param1);
    }

    public function setParam2(string $name): void
    {
        $this->param2 = $name;
    }

    public function getParam2() : string
    {
        return ':'.$this->param2;
    }

    public function isParam2() : bool
    {
        return isset($this->param2);
    }

    private function between(): CompositeExpression
    {
        $field = $this->getField();
        return new CompositeExpression(CompositeExpression::TYPE_AND, [
            $this->exprBuilder->gte($field, $this->getParam1()),
            $this->exprBuilder->lte($field, $this->getParam2())
        ]);
    }

    private function notBetween(): CompositeExpression
    {
        $field = $this->getField();
        return new CompositeExpression(CompositeExpression::TYPE_OR, [
            $this->exprBuilder->lt($field, $this->getParam1()),
            $this->exprBuilder->gt($field, $this->getParam2())
        ]);
    }

    public function execute() : string|CompositeExpression
    {
        $operator = $this->getOperator();
        $method = $operator->expr();
        // when need to call custom methods
        if($operator->isBetween()) {
            return call_user_func([$this, $method]);
        }
        // when need to call basic ExpressionBuilder methods
        $args = [$this->getField()];
        if(!$operator->isNull())
            $args[] = $this->getParam1();
        return call_user_func_array([$this->exprBuilder, $method], $args);
    }
}