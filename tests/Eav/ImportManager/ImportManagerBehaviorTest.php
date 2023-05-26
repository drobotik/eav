<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportManager;

use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Import\ImportManager;
use PHPUnit\Framework\TestCase;

class ImportManagerBehaviorTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\ImportManager::run
     */
    public function run_manager()
    {
        $attributesWorker = $this->getMockBuilder(\Drobotik\Eav\Import\Attributes\Worker::class)
            ->onlyMethods(['run'])->getMock();
        $attributesWorker->expects($this->once())->method('run');

        $contentWorker = $this->getMockBuilder(\Drobotik\Eav\Import\Content\Worker::class)
            ->onlyMethods(['run'])->getMock();
        $contentWorker->expects($this->once())->method('run');

        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getAttributesWorker', 'getContentWorker'])
            ->getMock();
        $container->expects($this->once())->method('getAttributesWorker')->willReturn($attributesWorker);
        $container->expects($this->once())->method('getContentWorker')->willReturn($contentWorker);

        $manager = $this->getMockBuilder(ImportManager::class)
            ->onlyMethods(['getContainer'])->getMock();
        $manager->expects($this->once())->method('getContainer')->willReturn($container);

        $manager->run();
    }
}