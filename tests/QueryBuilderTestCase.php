<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

use Drobotik\Eav\QueryBuilder\QueryBuilderRule;
use Illuminate\Database\Connection as Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar as MySQLGrammar;
use Illuminate\Database\Query\Processors\MySqlProcessor as MySQLProcessor;

class QueryBuilderTestCase extends \PHPUnit\Framework\TestCase
{
    protected QueryBuilderRule $rule;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new QueryBuilderRule();
    }
    protected function getQuery() : Builder
    {
        $pdo = new \PDO('sqlite::memory:');
        return new Builder(new Connection($pdo), new MySQLGrammar(), new MySQLProcessor());
    }
}