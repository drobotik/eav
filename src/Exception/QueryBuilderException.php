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

    /**
     * @throws QueryBuilderException
     */
    public static function unsupportedOperator(string $case)
    {
        throw new static(sprintf(self::UNSUPPORTED_OPERATOR, $case));
    }
}