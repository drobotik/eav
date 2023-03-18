<?php
namespace Tests;

use Kuperwood\Eav\Migrator;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {
        (new Migrator())->migrate();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        (new Migrator())->rollback();
        parent::tearDown();
    }

}