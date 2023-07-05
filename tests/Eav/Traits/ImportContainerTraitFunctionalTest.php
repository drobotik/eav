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
use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Trait\ContainerTrait;
use Drobotik\Eav\Trait\ImportContainerTrait;
use PHPUnit\Framework\TestCase;

class ImportContainerTraitFunctionalTest extends TestCase
{
    use ImportContainerTrait;
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Trait\ImportContainerTrait::getContainer
     * @covers \Drobotik\Eav\Trait\ImportContainerTrait::getContainer
     */
    public function getter_setter()
    {
        $container = new ImportContainer();
        $this->setContainer($container);
        $this->assertSame($container, $this->getContainer());
    }
}