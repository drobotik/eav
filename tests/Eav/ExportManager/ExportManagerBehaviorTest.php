<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ExportManager;

use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Export\ExportManager;
use Drobotik\Eav\QueryBuilder\QueryBuilderManager;
use Drobotik\Eav\Result\Result;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use PHPUnit\Framework\TestCase;

class ExportManagerBehaviorTest extends TestCase
{
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Export\ExportManager::run
     */
    public function run_manager()
    {
        $result = new Result();
        $columns = ['one', 'two'];

        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toArray', 'map'])->getMock();
        $collection ->expects($this->once())->method('map')
            ->willReturn($collection);
        $collection->expects($this->once())
            ->method('toArray')
            ->willReturn([123]);

        $query = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])->getMock();
        $query->expects($this->once())->method('get')
            ->willReturn($collection);

        $queryBuilderManager = $this->getMockBuilder(QueryBuilderManager::class)
            ->onlyMethods(['run', 'getColumns'])->getMock();
        $queryBuilderManager->expects($this->once())->method('run')
            ->willReturn($query);
        $queryBuilderManager->expects($this->once())->method('getColumns')
            ->willReturn($columns);

        $driver = $this->getMockBuilder(CsvDriver::class)
            ->onlyMethods(['writeAll', 'setHeader'])->getMock();
        $driver->expects($this->once())->method('writeAll')
            ->with([123])
            ->willReturn($result);
        $driver->expects($this->once())->method('setHeader')
            ->with(array_merge([_ENTITY::ID->column()], $columns));

        $manager = $this->getMockBuilder(ExportManager::class)
            ->onlyMethods(['getDriver', 'getQueryBuilderManager'])
            ->getMock();

        $manager->expects($this->once())->method('getDriver')
            ->willReturn($driver);
        $manager->expects($this->once())->method('getQueryBuilderManager')
            ->willReturn($queryBuilderManager);

        $this->assertSame($result, $manager->run());
    }
}