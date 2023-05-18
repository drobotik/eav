<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Trait;

use Drobotik\Eav\QueryBuilder\QueryBuilder;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use Drobotik\Eav\QueryBuilder\QueryBuilderParser;

trait QueryBuilderSingletons
{
    public function makeQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder();
    }

    public function makeQueryBuilderAttributes(): QueryBuilderAttributes
    {
        return new QueryBuilderAttributes();
    }

    public function makeQueryBuilderParser() : QueryBuilderParser
    {
        return new QueryBuilderParser();
    }
}