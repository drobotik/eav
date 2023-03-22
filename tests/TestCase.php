<?php
namespace Tests;

use Kuperwood\Eav\Migrator;
use Doctrine\DBAL\Connection as DBALConnection;
use Kuperwood\Eav\ModelsManager;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected DBALConnection $connection;

    protected function setUp() : void
    {
        parent::setUp();
        $this->em = ModelsManager::getMe();
        $migrator = new Migrator();
        $migrator->rollback();
        $migrator->migrate();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

}