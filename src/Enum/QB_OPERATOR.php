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

class QB_OPERATOR
{
    public const EQUAL = 'equal';
    public const NOT_EQUAL = 'not_equal';
    public const IN = 'in';
    public const NOT_IN = 'not_in';
    public const LESS = 'less';
    public const LESS_OR_EQUAL = 'less_or_equal';
    public const GREATER = 'greater';
    public const GREATER_OR_EQUAL = 'greater_or_equal';
    public const BETWEEN = 'between';
    public const NOT_BETWEEN = 'not_between';
    public const BEGINS_WITH = 'begins_with';
    public const NOT_BEGINS_WITH = 'not_begins_with';
    public const CONTAINS = 'contains';
    public const NOT_CONTAINS = 'not_contains';
    public const ENDS_WITH = 'ends_with';
    public const NOT_ENDS_WITH = 'not_ends_with';
    public const IS_EMPTY = 'is_empty';
    public const IS_NOT_EMPTY = 'is_not_empty';
    public const IS_NULL = 'is_null';
    public const IS_NOT_NULL = 'is_not_null';

    /**
     * @throws QueryBuilderException
     */
    public static function getCase(string $case) : string
    {
        switch ($case) {
            case self::EQUAL:
                return self::EQUAL;
            case self::NOT_EQUAL:
                return self::NOT_EQUAL;
            case self::IN:
                return self::IN;
            case self::NOT_IN:
                return self::NOT_IN;
            case self::LESS:
                return self::LESS;
            case self::LESS_OR_EQUAL:
                return self::LESS_OR_EQUAL;
            case self::GREATER:
                return self::GREATER;
            case self::GREATER_OR_EQUAL:
                return self::GREATER_OR_EQUAL;
            case self::BETWEEN:
                return self::BETWEEN;
            case self::NOT_BETWEEN:
                return self::NOT_BETWEEN;
            case self::BEGINS_WITH:
                return self::BEGINS_WITH;
            case self::NOT_BEGINS_WITH:
                return self::NOT_BEGINS_WITH;
            case self::CONTAINS:
                return self::CONTAINS;
            case self::NOT_CONTAINS:
                return self::NOT_CONTAINS;
            case self::ENDS_WITH:
                return self::ENDS_WITH;
            case self::NOT_ENDS_WITH:
                return self::NOT_ENDS_WITH;
            case self::IS_EMPTY:
                return self::IS_EMPTY;
            case self::IS_NOT_EMPTY:
                return self::IS_NOT_EMPTY;
            case self::IS_NULL:
                return self::IS_NULL;
            case self::IS_NOT_NULL:
                return self::IS_NOT_NULL;
            default:
                throw QueryBuilderException::unsupportedOperator($case);
        }
    }
    public static function isValueRequired(string $name): bool {
        switch ($name) {
            case self::EQUAL:
            case self::NOT_EQUAL:
            case self::IN:
            case self::NOT_IN:
            case self::LESS:
            case self::LESS_OR_EQUAL:
            case self::GREATER:
            case self::GREATER_OR_EQUAL:
            case self::BETWEEN:
            case self::NOT_BETWEEN:
            case self::BEGINS_WITH:
            case self::NOT_BEGINS_WITH:
            case self::CONTAINS:
            case self::NOT_CONTAINS:
            case self::ENDS_WITH:
            case self::NOT_ENDS_WITH:
                return true;

            case self::IS_EMPTY:
            case self::IS_NOT_EMPTY:
            case self::IS_NULL:
            case self::IS_NOT_NULL:
                return false;
        }
    }


    public static function applyTo(string $name): array {
        switch ($name) {
            case self::EQUAL:
            case self::NOT_EQUAL:
            case self::IN:
            case self::NOT_IN:
            case self::LESS:
            case self::LESS_OR_EQUAL:
            case self::GREATER:
            case self::GREATER_OR_EQUAL:
            case self::BETWEEN:
            case self::NOT_BETWEEN:
                return [
                    ATTR_TYPE::INTEGER,
                    ATTR_TYPE::DECIMAL,
                    ATTR_TYPE::DATETIME
                ];

            case self::BEGINS_WITH:
            case self::NOT_BEGINS_WITH:
            case self::CONTAINS:
            case self::NOT_CONTAINS:
            case self::ENDS_WITH:
            case self::NOT_ENDS_WITH:
            case self::IS_EMPTY:
            case self::IS_NOT_EMPTY:
                return [
                    ATTR_TYPE::STRING,
                    ATTR_TYPE::TEXT
                ];

            case self::IS_NULL:
            case self::IS_NOT_NULL:
                return [
                    ATTR_TYPE::STRING,
                    ATTR_TYPE::TEXT,
                    ATTR_TYPE::INTEGER,
                    ATTR_TYPE::DECIMAL,
                    ATTR_TYPE::DATETIME
                ];
        }
    }

    public static function sql(string $name): string {
        switch ($name) {
            case self::EQUAL:
            case self::IS_EMPTY:
                return '=';

            case self::NOT_EQUAL:
            case self::IS_NOT_EMPTY:
                return '!=';

            case self::IN:
                return 'IN';

            case self::NOT_IN:
                return 'NOT IN';

            case self::LESS:
                return '<';

            case self::LESS_OR_EQUAL:
                return '<=';

            case self::GREATER:
                return '>';

            case self::GREATER_OR_EQUAL:
                return '>=';

            case self::BETWEEN:
                return 'BETWEEN';

            case self::NOT_BETWEEN:
                return 'NOT BETWEEN';

            case self::BEGINS_WITH:
            case self::CONTAINS:
            case self::ENDS_WITH:
                return 'LIKE';

            case self::NOT_BEGINS_WITH:
            case self::NOT_CONTAINS:
            case self::NOT_ENDS_WITH:
                return 'NOT LIKE';

            case self::IS_NULL:
                return 'NULL';

            case self::IS_NOT_NULL:
                return 'NOT NULL';
        }
    }

    public static function prepend(string $name): string|bool {
        switch ($name) {
            case self::ENDS_WITH:
            case self::NOT_ENDS_WITH:
            case self::CONTAINS:
            case self::NOT_CONTAINS:
                return '%';

            default:
                return false;
        }
    }

    public static function append(string $name): string|bool {
        switch ($name) {
            case self::BEGINS_WITH:
            case self::NOT_BEGINS_WITH:
            case self::CONTAINS:
            case self::NOT_CONTAINS:
                return '%';

            default:
                return false;
        }
    }

    public static function isNeedsArray(string $name): bool {
        switch ($name) {
            case self::NOT_IN:
            case self::IN:
            case self::NOT_BETWEEN:
            case self::BETWEEN:
                return true;

            default:
                return false;
        }
    }

    public static function isNull(string $name): bool {
        switch ($name) {
            case self::IS_NOT_NULL:
            case self::IS_NULL:
                return true;

            default:
                return false;
        }
    }

    public static function isEmpty(string $name): bool {
        switch ($name) {
            case self::IS_EMPTY:
            case self::IS_NOT_EMPTY:
                return true;

            default:
                return false;
        }
    }

    public static function isBetween(string $name): bool {
        switch ($name) {
            case self::BETWEEN:
            case self::NOT_BETWEEN:
                return true;

            default:
                return false;
        }
    }

    public static function isLike(string $name): bool {
        switch ($name) {
            case self::BEGINS_WITH:
            case self::CONTAINS:
            case self::ENDS_WITH:
            case self::NOT_BEGINS_WITH:
            case self::NOT_ENDS_WITH:
            case self::NOT_CONTAINS:
                return true;

            default:
                return false;
        }
    }

    public static function expr(string $name): string {
        switch ($name) {
            case self::EQUAL:
            case self::IS_EMPTY:
                return 'eq';

            case self::NOT_EQUAL:
            case self::IS_NOT_EMPTY:
                return 'neq';

            case self::IN:
                return 'in';

            case self::NOT_IN:
                return 'notIn';

            case self::LESS:
                return 'lt';

            case self::LESS_OR_EQUAL:
                return 'lte';

            case self::GREATER:
                return 'gt';

            case self::GREATER_OR_EQUAL:
                return 'gte';

            case self::BETWEEN:
                return 'between';

            case self::NOT_BETWEEN:
                return 'notBetween';

            case self::BEGINS_WITH:
            case self::CONTAINS:
            case self::ENDS_WITH:
                return 'like';

            case self::NOT_BEGINS_WITH:
            case self::NOT_CONTAINS:
            case self::NOT_ENDS_WITH:
                return 'notLike';

            case self::IS_NULL:
                return 'isNull';

            case self::IS_NOT_NULL:
                return 'isNotNull';
        }
    }
}