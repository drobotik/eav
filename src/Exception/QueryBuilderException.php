<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Exception;

class QueryBuilderException extends \Exception
{
    public const UNSUPPORTED_OPERATOR = "unsupported operator %s";
    public const UNSUPPORTED_ATTRIBUTE = "unsupported attribute %s";
    public const UNSUPPORTED_CONDITION = "unsupported condition %s";
    /**
     * @throws QueryBuilderException
     */
    public static function unsupportedOperator(string $operator)
    {
        throw new static(sprintf(self::UNSUPPORTED_OPERATOR, $operator));
    }
    /**
     * @throws QueryBuilderException
     */
    public static function unsupportedAttribute(string $name)
    {
        throw new static(sprintf(self::UNSUPPORTED_ATTRIBUTE, $name));
    }
    /**
     * @throws QueryBuilderException
     */
    public static function unsupportedCondition(string $name)
    {
        throw new static(sprintf(self::UNSUPPORTED_CONDITION, $name));
    }
}