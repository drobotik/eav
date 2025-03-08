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

class QB_CONDITION
{
    public const AND = 'and';
    public const OR = 'or';

    /**
     * @throws QueryBuilderException
     */
    public static function getCase(string $slug)
    {
        switch ($slug) {
            case self::AND:
                return self::AND;
            case self::OR:
                return self::OR;
            default:
                return QueryBuilderException::unsupportedCondition($slug);
        }
    }
}