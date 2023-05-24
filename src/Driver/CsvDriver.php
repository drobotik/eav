<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Driver;

use Drobotik\Eav\Result\Result;
use Drobotik\Eav\TransportDriver;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

class CsvDriver extends TransportDriver
{
    private Reader $reader;
    private Writer $writer;

    public function setReader(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function getReader() : Reader
    {
        return $this->reader;
    }

    public function setWriter(Writer $writer)
    {
        $this->writer = $writer;
    }

    public function getWriter() : Writer
    {
        return $this->writer;
    }

    public function getHeader(): array
    {
        return $this->getReader()->getHeader();
    }

    public function getChunk() : array|null
    {
        $cursor = $this->getCursor();
        $chunkSize = $this->getChunkSize();
        $csv = $this->getReader();

        $stmt = new Statement();
        $stmt = $stmt->offset($cursor)->limit($chunkSize);
        $records = $stmt->process($csv);
        $outputSize = $records->count();

        if($outputSize == 0)
        {
            return null;
        }
        $output = [];
        foreach ($records as $record) {
            $output[] = $record;
        }
        $this->setCursor($cursor + $outputSize);
        return $output;
    }

    public function readAll() : array
    {
        $reader = $this->getReader();
        $records = $reader->getRecords();
        $output = [];
        foreach ($records as $record) {
            $output[] = $record;
        }
        return $output;
    }

    public function writeAll(array $data) : Result
    {
        $result = new Result();
        $writer = $this->getWriter();
        $writer->insertAll($data);
        $result->exportSuccess();
        return $result;
    }
}