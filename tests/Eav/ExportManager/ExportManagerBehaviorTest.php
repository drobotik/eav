<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ExportManager;

use Drobotik\Eav\Export\Driver\ExportCsvDriver;
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

        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['toArray'])->getMock();
        $collection->expects($this->once())
            ->method('toArray')
            ->willReturn([123]);

        $query = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])->getMock();
        $query->expects($this->once())->method('get')
            ->willReturn($collection);

        $queryBuilderManager = $this->getMockBuilder(QueryBuilderManager::class)
            ->onlyMethods(['run'])->getMock();
        $queryBuilderManager->expects($this->once())->method('run')
            ->willReturn($query);

        $driver = $this->getMockBuilder(ExportCsvDriver::class)
            ->onlyMethods(['run'])->getMock();
        $driver->expects($this->once())->method('run')
            ->with([123])
            ->willReturn($result);

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