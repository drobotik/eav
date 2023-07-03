<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Enum;

use Drobotik\Eav\Exception\QueryBuilderException;

enum QB_CONDITION
{
    case AND;
    case OR;

    public function name() : string
    {
        return match ($this) {
            self::AND => "and",
            self::OR => "or"
        };
    }

    public static function getCase(string $slug)
    {
        return match($slug) {
            self::AND->name() => self::AND,
            self::OR->name() => self::OR,
            default => QueryBuilderException::unsupportedCondition($slug)
        };
    }
}