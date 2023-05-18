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

use Carbon\Carbon;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Export\Driver\ExportCsvDriver;
use Drobotik\Eav\Result\Result;
use Tests\TestCase;

class ExportCsvDriverAcceptanceTest extends TestCase
{
    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\Export\Driver\ExportCsvDriver::run
     */
    public function results()
    {
        $driver = new ExportCsvDriver();
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
        foreach($input as $row)
        {
            $data[] = [
                ATTR_TYPE::STRING->value() => $row[0],
                ATTR_TYPE::INTEGER->value() => $row[1],
                ATTR_TYPE::DECIMAL->value() => $row[2],
                ATTR_TYPE::DATETIME->value() => $row[3],
                ATTR_TYPE::TEXT->value() => $row[4]
            ];
        }
        $path = dirname(__DIR__, 2) . '/temp/csv.csv';
        $driver->setPath($path);
        $result = $driver->run($data);
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

}
