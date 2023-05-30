<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\CsvDriver;

use Carbon\Carbon;
use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\TransportDriver;
use League\Csv\Reader;
use League\Csv\Writer;
use SplFileObject;
use Tests\TestCase;

class CsvDriverFunctionalTest extends TestCase
{
    private TransportDriver $driver;
    private string $path;

    public function setUp(): void
    {
        parent::setUp();
        $this->driver = new CsvDriver();
        $this->path = dirname(__DIR__, 2) . '/Data/test.csv';
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::setReader
     * @covers \Drobotik\Eav\Driver\CsvDriver::getReader
     */
    public function reader()
    {
        $file = new SplFileObject($this->path);
        $reader = Reader::createFromFileObject($file);
        $this->driver->setReader($reader);
        $this->assertSame($reader, $this->driver->getReader());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::setReader
     * @covers \Drobotik\Eav\Driver\CsvDriver::getReader
     */
    public function writer()
    {
        $file = new SplFileObject($this->path);
        $writer = Writer::createFromFileObject($file);
        $this->driver->setWriter($writer);
        $this->assertSame($writer, $this->driver->getWriter());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::getHeader
     */
    public function get_header()
    {
        $file = new SplFileObject($this->path, 'r');
        $reader = Reader::createFromFileObject($file);
        $reader->setDelimiter(',');
        $reader->setHeaderOffset(0);
        $this->driver->setReader($reader);
        $result = $this->driver->getHeader();
        $this->assertEquals([
            _ENTITY::ID->column(),
            ATTR_TYPE::STRING->value(),
            ATTR_TYPE::INTEGER->value(),
            ATTR_TYPE::DECIMAL->value(),
            ATTR_TYPE::DATETIME->value(),
            ATTR_TYPE::TEXT->value()
        ], $result);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::getChunk
     */
    public function chunk()
    {
        $file = new SplFileObject($this->path, 'r');
        $reader = Reader::createFromFileObject($file);
        $reader->setDelimiter(',');
        $reader->setHeaderOffset(0);
        $this->driver->setReader($reader);
        $this->driver->setCursor(0);
        $this->driver->setChunkSize(10);

        $chunk = $this->driver->getChunk();
        $this->assertCount(10, $chunk);
        $this->assertEquals('sunt', $chunk[0][ATTR_TYPE::STRING->value()]);
        $this->assertEquals('repudiandae', $chunk[9][ATTR_TYPE::STRING->value()]);
        $this->assertEquals(10, $this->driver->getCursor());
        $chunk = $this->driver->getChunk();
        $this->assertCount(10, $chunk);
        $this->assertEquals('rerum', $chunk[0][ATTR_TYPE::STRING->value()]);
        $this->assertEquals('voluptatem', $chunk[9][ATTR_TYPE::STRING->value()]);
        $this->assertEquals(20, $this->driver->getCursor());
        $this->driver->setChunkSize(75);
        $chunk = $this->driver->getChunk();
        $this->assertCount(75, $chunk);
        $this->assertEquals('reprehenderit', $chunk[0][ATTR_TYPE::STRING->value()]);
        $this->assertEquals('quos', $chunk[74][ATTR_TYPE::STRING->value()]);
        $this->assertEquals(95, $this->driver->getCursor());
        $this->driver->setChunkSize(10);
        $chunk = $this->driver->getChunk();
        $this->assertCount(5, $chunk);
        $this->assertEquals('est', $chunk[0][ATTR_TYPE::STRING->value()]);
        $this->assertEquals('vel', $chunk[4][ATTR_TYPE::STRING->value()]);
        $this->assertEquals(100, $this->driver->getCursor());

        $chunk = $this->driver->getChunk();
        $this->assertNull($chunk);
        $this->assertEquals(100, $this->driver->getCursor());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::writeAll
     */
    public function write_all()
    {
        $driver = new CsvDriver();
        $input = [
            [
                ATTR_TYPE::STRING->value(),
                ATTR_TYPE::INTEGER->value(),
                ATTR_TYPE::DECIMAL->value(),
                ATTR_TYPE::DATETIME->value(),
                ATTR_TYPE::TEXT->value()
            ],
            ['test1', '1', '1.2', Carbon::now()->toISOString(), 'text text1'],
            ['test2', '1', '1.2', Carbon::now()->subDays()->toISOString(), 'text text2'],
            ['test3', '1', '1.2', Carbon::now()->subDays(2)->toISOString(), 'text text3']
        ];
        $path = tempnam('/', 'csv.csv');
        $file = new SplFileObject($path, 'w+');
        $writer = Writer::createFromFileObject($file);
        $writer->setDelimiter(',');
        $driver->setWriter($writer);
        $result = $driver->writeAll($input);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFileExists($path);
        $fp = fopen($path, 'r');
        $output = [];
        while (($row = fgetcsv($fp)) !== false) {
            $output[] = $row;
        }
        fclose($fp);
        $this->assertEquals($input, $output);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::readAll
     */
    public function read_all()
    {
        $path = tempnam('/', 'csv.csv');
        $fp = fopen($path, 'w+');
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
        $expected = [];
        foreach($data as $index => $row)
        {
            fputcsv($fp, $row);
            if($index > 0) {
                $expected[] = [
                    ATTR_TYPE::STRING->value() => $row[0],
                    ATTR_TYPE::INTEGER->value() => $row[1],
                    ATTR_TYPE::DECIMAL->value() => $row[2],
                    ATTR_TYPE::DATETIME->value() => $row[3],
                    ATTR_TYPE::TEXT->value() => $row[4],
                ];
            }
        }
        fclose($fp);
        $driver = new CsvDriver();
        $file = new SplFileObject($path);
        $reader = Reader::createFromFileObject($file);
        $reader->setHeaderOffset(0);
        $reader->setDelimiter(',');
        $driver->setReader($reader);
        $result = $driver->readAll();
        $this->assertEquals($expected, $result);
    }

}
