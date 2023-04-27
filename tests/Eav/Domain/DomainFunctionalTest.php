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
use Drobotik\Eav\Result\Result;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\ExportDriverFixture;
use Tests\Fixtures\DomainDataDriverFixture;

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
     * @covers \Drobotik\Eav\Domain::getExportDriver
     * @covers \Drobotik\Eav\Domain::hasExportDriver
     * @covers \Drobotik\Eav\Domain::setExportDriver
     */
    public function exportDriver()
    {
        $driver = new ExportDriverFixture();
        $this->assertFalse($this->domain->hasExportDriver());
        $this->domain->setExportDriver($driver);
        $this->assertSAME($driver, $this->domain->getExportDriver());
        $this->assertTrue($this->domain->hasExportDriver());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Domain::getImportDriver
     * @covers \Drobotik\Eav\Domain::hasImportDriver
     * @covers \Drobotik\Eav\Domain::setImportDriver
     */
    public function importDriver()
    {
        $driver = new DomainDataDriverFixture();
        $this->assertFalse($this->domain->hasImportDriver());
        $this->domain->setImportDriver($driver);
        $this->assertSAME($driver, $this->domain->getImportDriver());
        $this->assertTrue($this->domain->hasImportDriver());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Domain::import
     */
    public function importResult()
    {
        $driver = new DomainDataDriverFixture();
        $result = (new Result())->importSuccess();
        $driver->setResult($result);
        $this->domain->setImportDriver($driver);
        $this->assertSame($result, $this->domain->import([]));
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Domain::import
     */
    public function exportResult()
    {
        $driver = new ExportDriverFixture();
        $result = (new Result())->exportSuccess();
        $driver->setResult($result);
        $this->domain->setExportDriver($driver);
        $this->assertSame($result, $this->domain->export());
    }
}
