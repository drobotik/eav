<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\PivotEnum;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_SET;
use PHPUnit\Framework\TestCase;

class PivotEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_PIVOT::table
     */
    public function table() {
        $this->assertEquals('eav_pivot', _PIVOT::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_PIVOT::column
     */
    public function columns() {
        $cases = [];
        foreach (_PIVOT::cases() as $case) {
            $cases[$case->column()] = $case->column();
        }
        $this->assertEquals([
            _PIVOT::ID->column() => 'pivot_id',
            _PIVOT::DOMAIN_ID->column() => _DOMAIN::ID->column(),
            _PIVOT::SET_ID->column() => _SET::ID->column(),
            _PIVOT::GROUP_ID->column() => _GROUP::ID->column(),
            _PIVOT::ATTR_ID->column() => _ATTR::ID->column()
        ], $cases);
    }

}
