<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Domain;

use Drobotik\Eav\Domain;
use Drobotik\Eav\Export\ExportManager;
use Drobotik\Eav\Import\ImportManager;
use PHPUnit\Framework\TestCase;

class DomainFunctionalTest extends TestCase
{
    private Domain $domain;

    public function setUp(): void
    {
        parent::setUp();
        $this->domain = new Domain();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Domain::getImportManager
     * @covers \Drobotik\Eav\Domain::setImportManager
     */
    public function importManager()
    {
        $manager = new ImportManager();
        $this->domain->setImportManager($manager);
        $this->assertSame($manager, $this->domain->getImportManager());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Domain::getExportManager
     * @covers \Drobotik\Eav\Domain::setExportManager
     */
    public function exportManager()
    {
        $manager = new ExportManager();
        $this->domain->setExportManager($manager);
        $this->assertSame($manager, $this->domain->getExportManager());
    }

}
