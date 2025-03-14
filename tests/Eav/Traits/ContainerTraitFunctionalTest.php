<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Traits;

use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\Traits\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ContainerTraitFunctionalTest extends TestCase
{
    use ContainerTrait;
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Traits\ContainerTrait::setAttributeContainer
     * @covers \Drobotik\Eav\Traits\ContainerTrait::getAttributeContainer
     */
    public function getter_setter()
    {
        $container = new AttributeContainer();
        $this->setAttributeContainer($container);
        $this->assertSame($container, $this->getAttributeContainer());
    }
}