<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\DependencyContainer;

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