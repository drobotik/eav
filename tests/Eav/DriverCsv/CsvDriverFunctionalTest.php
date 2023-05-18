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

use Carbon\Carbon;
use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Result\Result;
use Tests\TestCase;

class CsvDriverFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::write
     */
    public function write()
    {
        $driver = new CsvDriver();
        $input = [
            ['test1', 1, 1.2, Carbon::now()->toISOString(), 'text text1'],
            ['test2', 1, 1.2, Carbon::now()->subDays()->toISOString(), 'text text2'],
            ['test3', 1, 1.2, Carbon::now()->subDays(2)->toISOString(), 'text text3']
        ];
        $header = [
            ATTR_TYPE::STRING->value(),
            ATTR_TYPE::INTEGER->value(),
            ATTR_TYPE::DECIMAL->value(),
            ATTR_TYPE::DATETIME->value(),
            ATTR_TYPE::TEXT->value()
        ];
        $data = [];
        foreach ($input as $row) {
            $data[] = [
                ATTR_TYPE::STRING->value()   => $row[0],
                ATTR_TYPE::INTEGER->value()  => $row[1],
                ATTR_TYPE::DECIMAL->value()  => $row[2],
                ATTR_TYPE::DATETIME->value() => $row[3],
                ATTR_TYPE::TEXT->value()     => $row[4]
            ];
        }
        $path = dirname(__DIR__, 2) . '/temp/csv.csv';
        $driver->setPath($path);
        $result = $driver->write($data);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFileExists($path);
        $fp = fopen($path, 'r');
        $output = [];
        while (($row = fgetcsv($fp)) !== false) {
            $output[] = $row;
        }
        fclose($fp);
        $expected = array_merge([$header], $input);
        $this->assertEquals($expected, $output);
    }

    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::read
     */
    public function read()
    {
        $path = dirname(__DIR__, 2) . '/temp/csv.csv';
        $fp = fopen($path, 'w');
        $data = [
            [
                ATTR_TYPE::STRING->value(),
                ATTR_TYPE::INTEGER->value(),
                ATTR_TYPE::DECIMAL->value(),
                ATTR_TYPE::DATETIME->value(),
                ATTR_TYPE::TEXT->value()
            ],
            ['test1', 1, 1.2, Carbon::now()->toISOString(), 'text text1'],
            ['test2', 1, 1.2, Carbon::now()->subDays()->toISOString(), 'text text2'],
            ['test3', 1, 1.2, Carbon::now()->subDays(2)->toISOString(), 'text text3']
        ];
        foreach($data as $row)
        {
            fputcsv($fp, $row);
        }
        fclose($fp);

        $driver = new CsvDriver();
        $driver->setPath($path);
        $result = $driver->read();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($data, $result->getData());
    }

}