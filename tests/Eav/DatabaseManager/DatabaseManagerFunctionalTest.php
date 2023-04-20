<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\DatabaseManager;

use Doctrine\ORM\EntityManager;
use Drobotik\Eav\Database\DatabaseManager;
use PHPUnit\Framework\TestCase;

class DatabaseManagerFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Database\DatabaseManager::initialize
     */
    public function initialize() {
        $result = DatabaseManager::initialize();
        $this->assertInstanceOf(EntityManager::class, $result);
        $result2 = DatabaseManager::initialize();
        $this->assertSame($result, $result2);
    }
}