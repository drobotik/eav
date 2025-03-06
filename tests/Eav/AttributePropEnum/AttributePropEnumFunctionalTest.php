<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributePropEnum;

use Drobotik\Eav\Enum\_ATTR_PROP;
use PHPUnit\Framework\TestCase;

class AttributePropEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_ATTR_PROP::table
     */
    public function table() {
        $this->assertEquals('eav_attribute_properties', _ATTR_PROP::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_ATTR_PROP::KEY
     * @covers \Drobotik\Eav\Enum\_ATTR_PROP::ATTRIBUTE_KEY
     * @covers \Drobotik\Eav\Enum\_ATTR_PROP::NAME
     * @covers \Drobotik\Eav\Enum\_ATTR_PROP::VALUE
     */
    public function column() {
        $this->assertEquals('property_key',  _ATTR_PROP::KEY);
        $this->assertEquals('attribute_key',  _ATTR_PROP::ATTRIBUTE_KEY);
        $this->assertEquals('name',  _ATTR_PROP::NAME);
        $this->assertEquals('value',  _ATTR_PROP::VALUE);
    }

}
