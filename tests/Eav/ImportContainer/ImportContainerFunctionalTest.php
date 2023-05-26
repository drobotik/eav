<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportContainer;

use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Import\ImportManager;
use PHPUnit\Framework\TestCase;

class ImportContainerFunctionalTest extends TestCase
{
    private ImportContainer $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new ImportContainer();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\ImportContainer::getDomainKey
     * @covers \Drobotik\Eav\Import\ImportContainer::setDomainKey
     */
    public function domain_key()
    {
        $this->container->setDomainKey(123);
        $this->assertEquals(123, $this->container->getDomainKey());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\ImportContainer::getSetKey
     * @covers \Drobotik\Eav\Import\ImportContainer::setSetKey
     */
    public function set_key()
    {
        $this->container->setSetKey(123);
        $this->assertEquals(123, $this->container->getSetKey());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\ImportContainer::setManager
     * @covers \Drobotik\Eav\Import\ImportContainer::getManager
     */
    public function manager()
    {
        $manager = new ImportManager();
        $this->container->setManager($manager);
        $this->assertSame($manager, $this->container->getManager());
        $this->assertSame($this->container, $manager->getContainer());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\ImportContainer::getAttributesWorker
     * @covers \Drobotik\Eav\Import\ImportContainer::setAttributesWorker
     */
    public function attributes_worker()
    {
        $worker = new \Drobotik\Eav\Import\Attributes\Worker();
        $this->container->setAttributesWorker($worker);
        $this->assertSame($worker, $this->container->getAttributesWorker());
        $this->assertSame($this->container, $worker->getContainer());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\ImportContainer::getContentWorker
     * @covers \Drobotik\Eav\Import\ImportContainer::setContentWorker
     */
    public function content_worker()
    {
        $worker = new \Drobotik\Eav\Import\Content\Worker();
        $this->container->setContentWorker($worker);
        $this->assertSame($worker, $this->container->getContentWorker());
        $this->assertSame($this->container, $worker->getContainer());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\ImportContainer::getDriver
     * @covers \Drobotik\Eav\Import\ImportContainer::setDriver
     */
    public function driver()
    {
        $driver = new CsvDriver();
        $this->container->setDriver($driver);
        $this->assertSame($driver, $this->container->getDriver());
    }
}