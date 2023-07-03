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

enum QB_OPERATOR
{
    case EQUAL;
    case NOT_EQUAL;
    case IN;
    case NOT_IN;
    case LESS;
    case LESS_OR_EQUAL;
    case GREATER;
    case GREATER_OR_EQUAL;
    case BETWEEN;
    case NOT_BETWEEN;
    case BEGINS_WITH;
    case NOT_BEGINS_WITH;
    case CONTAINS;
    case NOT_CONTAINS;
    case ENDS_WITH;
    case NOT_ENDS_WITH;
    case IS_EMPTY;
    case IS_NOT_EMPTY;
    case IS_NULL;
    case IS_NOT_NULL;


    public function name(): string {
        return match ($this) {
            self::EQUAL => 'equal',
            self::NOT_EQUAL => 'not_equal',
            self::IN => 'in',
            self::NOT_IN => 'not_in',
            self::LESS => 'less',
            self::LESS_OR_EQUAL => 'less_or_equal',
            self::GREATER => 'greater',
            self::GREATER_OR_EQUAL => 'greater_or_equal',
            self::BETWEEN => 'between',
            self::NOT_BETWEEN => 'not_between',
            self::BEGINS_WITH => 'begins_with',
            self::NOT_BEGINS_WITH => 'not_begins_with',
            self::CONTAINS => 'contains',
            self::NOT_CONTAINS => 'not_contains',
            self::ENDS_WITH => 'ends_with',
            self::NOT_ENDS_WITH => 'not_ends_with',
            self::IS_EMPTY => 'is_empty',
            self::IS_NOT_EMPTY => 'is_not_empty',
            self::IS_NULL => 'is_null',
            self::IS_NOT_NULL => 'is_not_null',
        };
    }
    /**
     * @throws QueryBuilderException
     */
    public static function getCase(string $case)
    {
        return match ($case) {
            self::EQUAL->name() => self::EQUAL,
            self::NOT_EQUAL->name() => self::NOT_EQUAL,
            self::IN->name() => self::IN,
            self::NOT_IN->name() => self::NOT_IN,
            self::LESS->name() => self::LESS,
            self::LESS_OR_EQUAL->name() => self::LESS_OR_EQUAL,
            self::GREATER->name() => self::GREATER,
            self::GREATER_OR_EQUAL->name() => self::GREATER_OR_EQUAL,
            self::BETWEEN->name() => self::BETWEEN,
            self::NOT_BETWEEN->name() => self::NOT_BETWEEN,
            self::BEGINS_WITH->name() => self::BEGINS_WITH,
            self::NOT_BEGINS_WITH->name() => self::NOT_BEGINS_WITH,
            self::CONTAINS->name() => self::CONTAINS,
            self::NOT_CONTAINS->name() => self::NOT_CONTAINS,
            self::ENDS_WITH->name() => self::ENDS_WITH,
            self::NOT_ENDS_WITH->name() => self::NOT_ENDS_WITH,
            self::IS_EMPTY->name() => self::IS_EMPTY,
            self::IS_NOT_EMPTY->name() => self::IS_NOT_EMPTY,
            self::IS_NULL->name() => self::IS_NULL,
            self::IS_NOT_NULL->name() => self::IS_NOT_NULL,
            default => QueryBuilderException::unsupportedOperator($case),
        };
    }

    public function isValueRequired(): bool {
        return match ($this) {
            self::EQUAL,
            self::NOT_EQUAL,
            self::IN,
            self::NOT_IN,
            self::LESS,
            self::LESS_OR_EQUAL,
            self::GREATER,
            self::GREATER_OR_EQUAL,
            self::BETWEEN,
            self::NOT_BETWEEN,
            self::BEGINS_WITH,
            self::NOT_BEGINS_WITH,
            self::CONTAINS,
            self::NOT_CONTAINS,
            self::ENDS_WITH,
            self::NOT_ENDS_WITH => true,
            self::IS_EMPTY,
            self::IS_NOT_EMPTY,
            self::IS_NULL,
            self::IS_NOT_NULL => false,
        };
    }

    public function applyTo(): array {
        return match ($this) {
            self::EQUAL, self::NOT_EQUAL, self::IN, self::NOT_IN, self::LESS,
            self::LESS_OR_EQUAL, self::GREATER, self::GREATER_OR_EQUAL, self::BETWEEN,
            self::NOT_BETWEEN =>
            [ATTR_TYPE::INTEGER,
             ATTR_TYPE::DECIMAL,
             ATTR_TYPE::DATETIME],

            self::BEGINS_WITH, self::NOT_BEGINS_WITH, self::CONTAINS, self::NOT_CONTAINS,
            self::ENDS_WITH, self::NOT_ENDS_WITH, self::IS_EMPTY, self::IS_NOT_EMPTY =>
            [ATTR_TYPE::STRING,
             ATTR_TYPE::TEXT],

            self::IS_NULL, self::IS_NOT_NULL =>
            [ATTR_TYPE::STRING,
             ATTR_TYPE::TEXT,
             ATTR_TYPE::INTEGER,
             ATTR_TYPE::DECIMAL,
             ATTR_TYPE::DATETIME],
        };
    }
    public function sql(): string {
        return match ($this) {
            self::EQUAL,
            self::IS_EMPTY => '=',
            self::NOT_EQUAL,
            self::IS_NOT_EMPTY => '!=',
            self::IN => 'IN',
            self::NOT_IN => 'NOT IN',
            self::LESS => '<',
            self::LESS_OR_EQUAL => '<=',
            self::GREATER => '>',
            self::GREATER_OR_EQUAL => '>=',
            self::BETWEEN => 'BETWEEN',
            self::NOT_BETWEEN => 'NOT BETWEEN',
            self::BEGINS_WITH,
            self::CONTAINS,
            self::ENDS_WITH => 'LIKE',
            self::NOT_BEGINS_WITH,
            self::NOT_ENDS_WITH,
            self::NOT_CONTAINS => 'NOT LIKE',
            self::IS_NULL => 'NULL',
            self::IS_NOT_NULL => 'NOT NULL',
        };
    }

    public function prepend(): string|bool {
        return match ($this) {
            self::ENDS_WITH,
            self::NOT_ENDS_WITH,
            self::CONTAINS,
            self::NOT_CONTAINS => '%',
            default => false
        };
    }

    public function append(): string|bool {
        return match ($this) {
            self::BEGINS_WITH,
            self::NOT_BEGINS_WITH,
            self::CONTAINS,
            self::NOT_CONTAINS => '%',
            default => false
        };
    }

    public function isNeedsArray() : bool
    {
        return match ($this) {
            self::NOT_IN,
            self::IN,
            self::NOT_BETWEEN,
            self::BETWEEN => true,
            default => false
        };
    }

    public function isNull() : bool
    {
        return match ($this) {
            self::IS_NOT_NULL,
            self::IS_NULL => true,
            default => false
        };
    }

    public function isBetween() : bool
    {
        return match ($this) {
            self::BETWEEN,
            self::NOT_BETWEEN => true,
            default => false
        };
    }

    public function isLike() : bool
    {
        return match ($this) {
            self::BEGINS_WITH,
            self::CONTAINS,
            self::ENDS_WITH,
            self::NOT_BEGINS_WITH,
            self::NOT_ENDS_WITH,
            self::NOT_CONTAINS => true,
            default => false
        };
    }
}