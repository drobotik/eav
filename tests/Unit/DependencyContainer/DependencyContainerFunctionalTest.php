<?php

namespace Tests\Unit\DependencyContainer;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Validation\Factory;
use Drobotik\Eav\DependencyContainer;
use PHPUnit\Framework\TestCase;

class DependencyContainerFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers DependencyContainer::getValidator
     */
    public function validator() {
        $dependency = new DependencyContainer();
        $this->assertInstanceOf(Factory::class, $dependency->setValidator(new ArrayLoader())->getValidator());
    }
}