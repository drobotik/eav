<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Export;

use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Driver;
use Drobotik\Eav\QueryBuilder\QueryBuilderManager;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Trait\DomainTrait;

class ExportManager
{
    use DomainTrait;

    private Driver              $driver;
    private QueryBuilderManager $queryBuilderManager;

    public function setQueryBuilderManager(QueryBuilderManager $manager): void
    {
        $this->queryBuilderManager = $manager;
    }

    public function getQueryBuilderManager() : QueryBuilderManager
    {
        return $this->queryBuilderManager;
    }

    public function setDriver(Driver $driver): void
    {
        $this->driver = $driver;
    }

    public function getDriver(): Driver
    {
        return $this->driver;
    }

    public function hasDriver(): bool
    {
        return isset($this->driver);
    }

    public function run(): Result
    {
        $qbManager = $this->getQueryBuilderManager();
        $records = $qbManager
            ->run()
            ->get()
            ->map(function ($value) {
                // query builder return not Arrayable items (stdClass)
                return json_decode(json_encode($value), true);
            })
            ->toArray();
        $columns = $qbManager->getColumns();
        $header = array_merge([_ENTITY::ID->column()], $columns);
        $driver = $this->getDriver();
        $driver->setHeader($header);
        return $driver->writeAll($records);
    }


}