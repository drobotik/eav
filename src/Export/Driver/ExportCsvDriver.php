<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Export\Driver;

use Drobotik\Eav\ExportDriver;
use Drobotik\Eav\Result\Result;

class ExportCsvDriver extends ExportDriver
{
    private string $path;

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    public function run(array $data): Result
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
