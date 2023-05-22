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
use SplFileObject;

class CsvDriver extends TransportDriver
{
    private string $path;
    private string $mode;
    private string $delimiter    = ',';
    private int    $headerOffset = 0;
    private int $total;
    private int $cursor;

    private int $chunkSize;
    private SplFileObject $file;

    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function getDelimiter() : string
    {
        return $this->delimiter;
    }

    public function setHeaderOffset(int $offset) : void
    {
        $this->headerOffset = $offset;
    }

    public function getHeaderOffset() : int
    {
        return $this->headerOffset;
    }

    public function setTotal(int $total)
    {
        $this->total = $total;
    }

    public function getTotal() : int
    {
        return $this->total;
    }

    public function calculateTotal() : void
    {
        $i = 1;
        $file = $this->getStream();
        while (!$file->eof()) {
            $i++;
            $file->next();
        }
        $this->setTotal($i);
    }

    public function getChunkSize() : int
    {
        return $this->chunkSize;
    }

    public function setChunkSize(int $size)
    {
        $this->chunkSize = $size;
    }

    public function getCursor() : int
    {
        return $this->cursor;
    }


    public function setCursor(int $index) : void
    {
        $this->cursor = $index;
    }

    public function getStream(): SplFileObject
    {
        return $this->file;
    }

    public function isStream(): bool
    {
        return isset($this->file);
    }

    public function openStream() : SplFileObject
    {
        $this->file = new SplFileObject($this->getPath(), $this->getMode());
        return $this->file;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setMode(string $mode)
    {
        $this->mode = $mode;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getChunk() : array|null
    {
        if (!$this->isStream())
        {
            $this->openStream();
        }
        $file = $this->getStream();
        $cursor = $this->getCursor();
        $chunkSize = $this->getChunkSize();
        $csv = Reader::createFromFileObject($file);
        $csv->setDelimiter($this->getDelimiter());
        $csv->setHeaderOffset($this->getHeaderOffset());

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

    public function getAll() : Result
    {
        $result = new Result();
        $fp = fopen($this->getPath(), 'r');
        $output = [];
        while (($row = fgetcsv($fp)) !== false) {
            $output[] = $row;
        }
        fclose($fp);
        $result->setData($output);
        return $result;
    }

    public function write(array $data) : Result
    {
        $result = new Result();
        $fp = fopen($this->getPath(), 'w');
        $header = count($data) < 1
            ? []
            : array_keys($data[0]);

        fputcsv($fp, $header);
        foreach($data as $row)
        {
            fputcsv($fp, $row);
        }
        fclose($fp);
        $result->exportSuccess();
        return $result;
    }
}