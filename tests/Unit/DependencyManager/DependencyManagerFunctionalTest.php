<?php

namespace Tests\Unit\DependencyManager;

use Kuperwood\Eav\DependencyContainer;
use Kuperwood\Eav\DependencyManager;
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