<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\DriverCsv;

use Drobotik\Eav\Driver\CsvDriver;
use Faker\Factory;
use Tests\PerformanceTestCase;

class CsvDriverPerformanceTest extends PerformanceTestCase
{
    /**
     * @test
     *
     * @group performance
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::write
     */
    public function write_twenty_thousands_records() {
        $driver = new CsvDriver();
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
        $driver->write($data);
        $end = microtime(true);
        $this->assertLessThan(120, $end - $start);
    }

}
