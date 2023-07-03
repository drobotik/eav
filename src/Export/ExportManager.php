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
use Drobotik\Eav\QueryBuilder\Config;
use Drobotik\Eav\QueryBuilder\QueryBuilder;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Trait\DomainTrait;
use Drobotik\Eav\Trait\SingletonsTrait;

class ExportManager
{
    use DomainTrait;
    use SingletonsTrait;

    private Driver $driver;

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

    public function run(int $domainKey, int $setKey, array $filters, array $columns): Result
    {
        $qb = new QueryBuilder();
        $config = new Config();
        $config->setDomainKey($domainKey);
        $config->setSetKey($setKey);
        $config->addAttributes($this->makeAttributeSetModel()->findAttributes($domainKey, $setKey));
        $config->addColumns($columns);
        $config->parse($filters);
        $qb->setConfig($config);
        $records = $qb->run()
            ->executeQuery()
            ->fetchAllAssociative();

        $header = array_merge([_ENTITY::ID->column()], $columns);
        $driver = $this->getDriver();
        $driver->setHeader($header);
        return $driver->writeAll($records);
    }


}