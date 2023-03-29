<?php

namespace Tests\Unit;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Validation\Factory;
use Kuperwood\Eav\Dependency;
use PHPUnit\Framework\TestCase;

class DependencyTest extends TestCase
{
    /** @test */
    public function validator() {
        $dependency = new Dependency();
        $this->assertInstanceOf(
            Factory::class,
            $dependency->setValidator(new ArrayLoader())->getValidator()
        );
    }
}