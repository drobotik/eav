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

class CsvDriver extends TransportDriver
{
    private string $path;

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function read() : Result
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