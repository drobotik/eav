<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\GroupEnum;

use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Enum\_SET;
use PHPUnit\Framework\TestCase;

class GroupEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_GROUP::table
     */
    public function table() {
        $this->assertEquals('eav_attribute_groups', _GROUP::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_GROUP::column
     */
    public function columns() {
        $cases = [];
        foreach (_GROUP::cases() as $case) {
            $cases[$case->column()] = $case->column();
        }
        $this->assertEquals([
            _GROUP::ID->column() => 'group_id',
            _GROUP::NAME->column() => 'name',
            _GROUP::SET_ID->column() => _SET::ID->column()
        ], $cases);
    }

}
