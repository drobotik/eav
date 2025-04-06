<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Traits;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\Traits\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ContainerTraitFunctionalTest extends TestCase
{
    use ContainerTrait;
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Traits\ContainerTrait::setAttributeContainer
     * @covers \Kuperwood\Eav\Traits\ContainerTrait::getAttributeContainer
     */
    public function getter_setter()
    {
        $container = new AttributeContainer();
        $this->setAttributeContainer($container);
        $this->assertSame($container, $this->getAttributeContainer());
    }
}