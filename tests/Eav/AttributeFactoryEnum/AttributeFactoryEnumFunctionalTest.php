<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeFactoryEnum;

use Kuperwood\Eav\Enum\_DOMAIN;
use Kuperwood\Eav\Enum\ATTR_FACTORY;
use PHPUnit\Framework\TestCase;

class AttributeFactoryEnumFunctionalTest extends TestCase
{

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\ATTR_FACTORY::ATTRIBUTE
     * @covers \Kuperwood\Eav\Enum\ATTR_FACTORY::GROUP
     * @covers \Kuperwood\Eav\Enum\ATTR_FACTORY::VALUE
     */
    public function columns() {
        $this->assertEquals('attribute', ATTR_FACTORY::ATTRIBUTE);
        $this->assertEquals('group', ATTR_FACTORY::GROUP);
        $this->assertEquals('value', ATTR_FACTORY::VALUE);
    }

}
