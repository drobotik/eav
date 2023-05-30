<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ExportManager;

use Drobotik\Eav\Domain;
use Drobotik\Eav\Export\ExportManager;
use Drobotik\Eav\QueryBuilder\QueryBuilderManager;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\DriverFixture;

class ExportManagerFunctionalTest extends TestCase
{
    private ExportManager $manager;

    public function setUp(): void
    {
        parent::setUp();
        $this->manager = new ExportManager();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Export\ExportManager::setDomain
     * @covers \Drobotik\Eav\Export\ExportManager::getDomain
     */
    public function domain()
    {
        $domain = new Domain();
        $this->manager->setDomain($domain);
        $this->assertSame($domain, $this->manager->getDomain());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Export\ExportManager::hasDriver
     * @covers \Drobotik\Eav\Export\ExportManager::getDriver
     * @covers \Drobotik\Eav\Export\ExportManager::setDriver
     */
    public function driver()
    {
        $driver = new DriverFixture();
        $this->assertFalse($this->manager->hasDriver());
        $this->manager->setDriver($driver);
        $this->assertSAME($driver, $this->manager->getDriver());
        $this->assertTrue($this->manager->hasDriver());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Export\ExportManager::getQueryBuilderManager
     * @covers \Drobotik\Eav\Export\ExportManager::setQueryBuilderManager
     */
    public function query_builder_manager()
    {
        $manager = new QueryBuilderManager();
        $this->manager->setQueryBuilderManager($manager);
        $this->assertSAME($manager, $this->manager->getQueryBuilderManager());
    }
}