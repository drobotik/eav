<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributePropEnum;

use Kuperwood\Eav\Enum\_ATTR_PROP;
use PHPUnit\Framework\TestCase;

class AttributePropEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_ATTR_PROP::table
     */
    public function table() {
        $this->assertEquals('eav_attribute_properties', _ATTR_PROP::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_ATTR_PROP::KEY
     * @covers \Kuperwood\Eav\Enum\_ATTR_PROP::ATTRIBUTE_KEY
     * @covers \Kuperwood\Eav\Enum\_ATTR_PROP::NAME
     * @covers \Kuperwood\Eav\Enum\_ATTR_PROP::VALUE
     */
    public function column() {
        $this->assertEquals('property_key',  _ATTR_PROP::KEY);
        $this->assertEquals('attribute_key',  _ATTR_PROP::ATTRIBUTE_KEY);
        $this->assertEquals('name',  _ATTR_PROP::NAME);
        $this->assertEquals('value',  _ATTR_PROP::VALUE);
    }

}
