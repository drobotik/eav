<?php

namespace Tests\Unit;

use Illuminate\Validation\Factory;
use Kuperwood\Eav\Container;
use Kuperwood\Eav\Dependency;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /** @test */
    public function get_instance() {
        $container = Container::getInstance();
        $this->assertInstanceOf(Dependency::class, $container);
        $this->assertInstanceOf(Factory::class, $container->getValidator());
    }
}