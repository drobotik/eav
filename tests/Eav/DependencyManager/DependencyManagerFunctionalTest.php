<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\DependencyManager;

use Drobotik\Eav\DependencyContainer;
use Drobotik\Eav\DependencyManager;
use PHPUnit\Framework\TestCase;

class DependencyManagerFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers DependencyManager::getContainer
     */
    public function get_container() {
        $container = DependencyManager::getContainer();
        $this->assertInstanceOf(DependencyContainer::class, $container);
    }
    /**
     * @test
     * @group functional
     * @covers DependencyManager::getDefaultContainer
     */
    public function get_default_container() {
        $container = DependencyManager::getDefaultContainer();
        $this->assertInstanceOf(DependencyContainer::class, $container);
    }
    /**
     * @test
     * @group functional
     * @covers DependencyManager::setContainer
     */
    public function set_container() {
        $container = DependencyManager::getDefaultContainer();
        $this->assertNotSame($container, DependencyManager::getContainer());
        DependencyManager::setContainer($container);
        $this->assertSame($container, $container);
    }
}