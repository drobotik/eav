<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Capsule\Manager as Capsule;

class PerformanceTestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => __DIR__.'/large.sqlite',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function tearDown(): void
    {
        $paths = [
            __DIR__.'/temp/csv.csv'
        ];
        foreach ($paths as $path)
            if(file_exists($path))
                unlink($path);
        parent::tearDown();
    }
}