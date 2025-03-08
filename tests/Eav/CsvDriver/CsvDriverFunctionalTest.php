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
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Driver;
use League\Csv\Reader;
use League\Csv\Writer;
use SplFileObject;
use Tests\TestCase;

class CsvDriverFunctionalTest extends TestCase
{
    private Driver $driver;
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
     * @covers \Drobotik\Eav\Driver\CsvDriver::setWriter
     * @covers \Drobotik\Eav\Driver\CsvDriver::getWriter
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
     * @covers \Drobotik\Eav\Driver\CsvDriver::setHeader
     * @covers \Drobotik\Eav\Driver\CsvDriver::isHeader
     */
    public function get_header()
    {
        $columns = [123,456];
        $this->assertFalse($this->driver->isHeader());
        $this->driver->setHeader([123,456]);
        $this->assertTrue($this->driver->isHeader());
        $this->assertEquals($columns, $this->driver->getHeader());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Driver\CsvDriver::getHeader
     */
    public function get_header_when_not_set()
    {
        $result = [123];
        $reader = $this->getMockBuilder(Reader::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getHeader'])
            ->getMock();

        $reader->expects($this->once())->method('getHeader')
            ->willReturn($result);

        $driver = $this->getMockBuilder(CsvDriver::class)
            ->onlyMethods(['getReader'])->getMock();
        $driver->expects($this->once())->method('getReader')
            ->willReturn($reader);

        $this->assertEquals($result, $driver->getHeader());
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
        $this->assertEquals('sunt', $chunk[0][ATTR_TYPE::STRING]);
        $this->assertEquals('repudiandae', $chunk[9][ATTR_TYPE::STRING]);
        $this->assertEquals(10, $this->driver->getCursor());
        $chunk = $this->driver->getChunk();
        $this->assertCount(10, $chunk);
        $this->assertEquals('rerum', $chunk[0][ATTR_TYPE::STRING]);
        $this->assertEquals('voluptatem', $chunk[9][ATTR_TYPE::STRING]);
        $this->assertEquals(20, $this->driver->getCursor());
        $this->driver->setChunkSize(75);
        $chunk = $this->driver->getChunk();
        $this->assertCount(75, $chunk);
        $this->assertEquals('reprehenderit', $chunk[0][ATTR_TYPE::STRING]);
        $this->assertEquals('quos', $chunk[74][ATTR_TYPE::STRING]);
        $this->assertEquals(95, $this->driver->getCursor());
        $this->driver->setChunkSize(10);
        $chunk = $this->driver->getChunk();
        $this->assertCount(5, $chunk);
        $this->assertEquals('est', $chunk[0][ATTR_TYPE::STRING]);
        $this->assertEquals('vel', $chunk[4][ATTR_TYPE::STRING]);
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
        $columns = [
            ATTR_TYPE::STRING,
            ATTR_TYPE::INTEGER,
            ATTR_TYPE::DECIMAL,
            ATTR_TYPE::DATETIME,
            ATTR_TYPE::TEXT
        ];
        $input = [
            ['test1', '1', '1.2', Carbon::now()->toISOString(), 'text text1'],
            ['test2', '1', '1.2', Carbon::now()->subDays()->toISOString(), 'text text2'],
            ['test3', '1', '1.2', Carbon::now()->subDays(2)->toISOString(), 'text text3']
        ];
        $path = dirname(__DIR__, 2) . '/Data/csv.csv';
        $file = new SplFileObject($path, 'w');
        $writer = Writer::createFromFileObject($file);

        $writer->setDelimiter(',');
        $driver->setWriter($writer);
        $driver->setHeader($columns);
        $result = $driver->writeAll($input);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFileExists($path);
        $fp = fopen($path, 'r');
        $output = [];
        while (($row = fgetcsv($fp)) !== false) {
            $output[] = $row;
        }
        fclose($fp);
        $expected = [];
        $expected[] = $columns;
        foreach ($input as $line)
            $expected[] = $line;
        $this->assertEquals($expected, $output);
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
        $path = dirname(__DIR__, 2) . '/Data/csv.csv';
        $fp = fopen($path, 'w');
        $data = [
            [
                ATTR_TYPE::STRING,
                ATTR_TYPE::INTEGER,
                ATTR_TYPE::DECIMAL,
                ATTR_TYPE::DATETIME,
                ATTR_TYPE::TEXT
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
                    ATTR_TYPE::STRING => $row[0],
                    ATTR_TYPE::INTEGER => $row[1],
                    ATTR_TYPE::DECIMAL => $row[2],
                    ATTR_TYPE::DATETIME => $row[3],
                    ATTR_TYPE::TEXT => $row[4],
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

    protected function tearDown(): void
    {
        $csv = new SplFileObject(dirname(__DIR__, 2) . '/Data/csv.csv', 'w');
        $csv->setCsvControl();
        $csv->fputcsv([], ',', '"');
    }

}
