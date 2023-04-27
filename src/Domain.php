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

use Drobotik\Eav\Result\Result;

class Domain
{
    private DomainDataDriver $exportDriver;
    private DomainDataDriver $importDriver;

    public function setExportDriver(DomainDataDriver $driver): void
    {
        $this->exportDriver = $driver;
    }

    public function getExportDriver(): DomainDataDriver
    {
        return $this->exportDriver;
    }

    public function hasExportDriver(): bool
    {
        return isset($this->exportDriver);
    }

    public function setImportDriver(DomainDataDriver $driver): void
    {
        $this->importDriver = $driver;
    }

    public function getImportDriver(): DomainDataDriver
    {
        return $this->importDriver;
    }

    public function hasImportDriver(): bool
    {
        return isset($this->importDriver);
    }

    public function import(): Result
    {
        return $this->getImportDriver()->run();
    }

    public function export(): Result
    {
        return $this->getExportDriver()->run();
    }
}
