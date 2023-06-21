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
     * @covers \Drobotik\Eav\Enum\_ATTR::column
     */
    public function columns() {
        $cases = [];
        foreach (_ATTR_PROP::cases() as $case) {
            $cases[$case->column()] = $case->column();
        }
        $this->assertEquals([
            _ATTR_PROP::KEY->column()           => 'property_key',
            _ATTR_PROP::ATTRIBUTE_KEY->column() => 'attribute_key',
            _ATTR_PROP::NAME->column()          => "name",
            _ATTR_PROP::VALUE->column()         => 'value',

        ], $cases);
    }

}
