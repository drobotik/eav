<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

use Drobotik\Eav\Factory\EavFactory;
use Faker\Generator;
use Illuminate\Database\Capsule\Manager as Capsule;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected EavFactory $eavFactory;
    protected Generator $faker;
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
        $this->eavFactory = new EavFactory();
        $this->faker = \Faker\Factory::create();
    }

    protected function tearDown(): void
    {
        $migrator = new Migrator();
        $migrator->rollback();
        parent::tearDown();
    }

}