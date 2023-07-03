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

class PerformanceTestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $dbParams = [
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ];
        Connection::get($dbParams);
    }

    public function tearDown(): void
    {
        Cleaner::run();
        parent::tearDown();
    }
}