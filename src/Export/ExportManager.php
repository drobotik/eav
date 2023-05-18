<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Export;

use Drobotik\Eav\TransportDriver;
use Drobotik\Eav\QueryBuilder\QueryBuilderManager;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Trait\DomainTrait;

class ExportManager
{
    use DomainTrait;

    private TransportDriver     $driver;
    private QueryBuilderManager $queryBuilderManager;

    public function setQueryBuilderManager(QueryBuilderManager $manager): void
    {
        $this->queryBuilderManager = $manager;
    }

    public function getQueryBuilderManager() : QueryBuilderManager
    {
        return $this->queryBuilderManager;
    }

    public function setDriver(TransportDriver $driver): void
    {
        $this->driver = $driver;
    }

    public function getDriver(): TransportDriver
    {
        return $this->driver;
    }

    public function hasDriver(): bool
    {
        return isset($this->driver);
    }

    public function run(): Result
    {
        return $this->getDriver()->write(
            $this->getQueryBuilderManager()
                ->run()
                ->get()
                ->toArray()
        );
    }


}