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

use Drobotik\Eav\Export\ExportManager;
use Drobotik\Eav\Import\ImportManager;
use Drobotik\Eav\Result\Result;

class Domain
{
    private ExportManager $exportManager;
    private ImportManager $importManager;

    public function getExportManager() : ExportManager
    {
        return $this->exportManager;
    }

    public function setExportManager(ExportManager $manager) : void
    {
        $manager->setDomain($this);
        $this->exportManager = $manager;
    }

    public function getImportManager() : ImportManager
    {
        return $this->importManager;
    }

    public function setImportManager(ImportManager $manager) : void
    {
        $manager->setDomain($this);
        $this->importManager = $manager;
    }

    public function import(): Result
    {
        return $this->getImportManager()->run();
    }

    public function export(int $domainKey, int $setKey, array $filters, array $columns): Result
    {
        return $this->getExportManager()->run($domainKey, $setKey, $filters, $columns);
    }
}
