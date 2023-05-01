<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ExportCsvDriver;

use Drobotik\Eav\Enum\EXPORT;
use Drobotik\Eav\Export\ExportCsvDriver;
use Tests\PerformanceTestCase;

class ExportCsvDriverPerformanceTest extends PerformanceTestCase
{
    /**
     * @test
     *
     * @group performance
     *
     * @covers \Drobotik\Eav\Export\ExportCsvDriver::run
     */
    public function twenty_thousands_records() {
        $driver = new ExportCsvDriver();
        $path = dirname(__DIR__, 2) . '/temp/csv.csv';
        $config = [
            EXPORT::PATH->field() => $path,
            EXPORT::DOMAIN_KEY->field() => 1,
            EXPORT::SET_KEY->field() => 1
        ];
        $driver->setData($config);
        $start = microtime(true);
        $driver->run();
        $end = microtime(true);
        $this->assertLessThan(120, $end - $start);
    }

}
