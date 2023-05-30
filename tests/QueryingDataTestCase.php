<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

use Drobotik\Eav\Database\Connection;
use Illuminate\Database\Capsule\Manager as Capsule;

class QueryingDataTestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $sqlitePath = dirname(__DIR__) . '/tests/large.sqlite';
        $dbParams = [
            'driver' => 'pdo_sqlite',
            'path' => $sqlitePath
        ];
        Connection::get($dbParams);
        Connection::pdo("sqlite:".$dbParams['path']);
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => $sqlitePath,
        ]);
        $this->capsule = $capsule;
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function tearDown(): void
    {
        Cleaner::run();
        parent::tearDown();
    }
}