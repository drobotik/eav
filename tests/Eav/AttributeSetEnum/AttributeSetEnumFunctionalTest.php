<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSetEnum;

use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\_SET;
use PHPUnit\Framework\TestCase;

class AttributeSetEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_SET::table
     */
    public function table() {
        $this->assertEquals('eav_attribute_sets', _SET::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_SET::column
     */
    public function columns() {
        $cases = [];
        foreach (_SET::cases() as $case) {
            $cases[$case->column()] = $case->column();
        }
        $this->assertEquals([
            _SET::ID->column() => 'set_id',
            _SET::NAME->column() => 'name',
            _SET::DOMAIN_ID->column() => _DOMAIN::ID->column()
        ], $cases);
    }

}
