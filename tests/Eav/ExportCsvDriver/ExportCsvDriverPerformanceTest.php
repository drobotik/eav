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

use Drobotik\Eav\Export\Driver\ExportCsvDriver;
use Faker\Factory;
use Tests\PerformanceTestCase;

class ExportCsvDriverPerformanceTest extends PerformanceTestCase
{
    /**
     * @test
     *
     * @group performance
     *
     * @covers \Drobotik\Eav\Export\Driver\ExportCsvDriver::run
     */
    public function twenty_thousands_records() {
        $driver = new ExportCsvDriver();
        $path = dirname(__DIR__, 2) . '/temp/csv.csv';
        $driver->setPath($path);

        $faker = Factory::create();

        $data = [];
        for ($i=0;$i<20000;$i++) {
            $data[] = [
                'name' => $faker->name(),
                'phone' => $faker->phoneNumber(),
                'note' => $faker->words(10, true),
                'float' => $faker->randomFloat(),
                'datetime' => $faker->dateTime->format('d.m.Y H:i:s')
            ];
        }
        $start = microtime(true);
        $driver->run($data);
        $end = microtime(true);
        $this->assertLessThan(120, $end - $start);
    }

}
