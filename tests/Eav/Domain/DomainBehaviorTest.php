<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Domain;

use Drobotik\Eav\Domain;
use Drobotik\Eav\Export\ExportManager;
use Drobotik\Eav\Import\ImportManager;
use Drobotik\Eav\Result\Result;
use PHPUnit\Framework\TestCase;

class DomainBehaviorTest extends TestCase
{
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Domain::export
     */
    public function export()
    {
        $result = new Result();
        $manager = $this->getMockBuilder(ExportManager::class)
            ->onlyMethods(['run'])
            ->getMock();
        $manager->expects($this->once())->method('run')
            ->willReturn($result);
        $domain = $this->getMockBuilder(Domain::class)
            ->onlyMethods(['getExportManager'])->getMock();
        $domain->expects($this->once())->method('getExportManager')
            ->willReturn($manager);
        $this->assertSame($result, $domain->export());
    }

    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Domain::import
     */
    public function import()
    {
        $result = new Result();
        $manager = $this->getMockBuilder(ImportManager::class)
            ->onlyMethods(['run'])
            ->getMock();
        $manager->expects($this->once())->method('run')
            ->willReturn($result);
        $domain = $this->getMockBuilder(Domain::class)
            ->onlyMethods(['getImportManager'])->getMock();
        $domain->expects($this->once())->method('getImportManager')
            ->willReturn($manager);
        $this->assertSame($result, $domain->import());
    }
}
