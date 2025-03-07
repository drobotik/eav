<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeFactoryEnum;

use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use PHPUnit\Framework\TestCase;

class AttributeFactoryEnumFunctionalTest extends TestCase
{

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\ATTR_FACTORY::ATTRIBUTE
     * @covers \Drobotik\Eav\Enum\ATTR_FACTORY::GROUP
     * @covers \Drobotik\Eav\Enum\ATTR_FACTORY::VALUE
     */
    public function columns() {
        $this->assertEquals('attribute', ATTR_FACTORY::ATTRIBUTE);
        $this->assertEquals('group', ATTR_FACTORY::GROUP);
        $this->assertEquals('value', ATTR_FACTORY::VALUE);
    }

}
