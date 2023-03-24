<?php
namespace Tests;

use Illuminate\Database\Capsule\Manager as Capsule;
use Kuperwood\Dev\Factory;


class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp() : void
    {
        parent::setUp();
        $migrator = new Migrator();
        $migrator->rollback();
        $migrator->migrate();
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => __DIR__.'/test.sqlite',
        ]);
        $this->capsule = $capsule;
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $this->factory = new Factory();
    }

    protected function tearDown(): void
    {
        $migrator = new Migrator();
        $migrator->rollback();
        parent::tearDown();
    }

}