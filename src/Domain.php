<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav;

use Drobotik\Eav\Interface\ExportDriverInterface;
use Drobotik\Eav\Interface\ImportDriverInterface;
use Drobotik\Eav\Result\Result;

class Domain
{
    private ExportDriverInterface $exportDriver;
    private ImportDriverInterface $importDriver;

    public function setExportDriver(ExportDriverInterface $driver): void
    {
        $this->exportDriver = $driver;
    }

    public function getExportDriver(): ExportDriverInterface
    {
        return $this->exportDriver;
    }

    public function hasExportDriver(): bool
    {
        return isset($this->exportDriver);
    }

    public function setImportDriver(ImportDriverInterface $driver): void
    {
        $this->importDriver = $driver;
    }

    public function getImportDriver(): ImportDriverInterface
    {
        return $this->importDriver;
    }

    public function hasImportDriver(): bool
    {
        return isset($this->importDriver);
    }

    public function import(array $data): Result
    {
        return $this->getImportDriver()->run($data);
    }

    public function export(): Result
    {
        return $this->getExportDriver()->run();
    }
}
