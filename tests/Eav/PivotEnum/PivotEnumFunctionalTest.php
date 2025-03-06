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
     * @covers \Drobotik\Eav\Enum\_PIVOT::ID
     * @covers \Drobotik\Eav\Enum\_PIVOT::DOMAIN_ID
     * @covers \Drobotik\Eav\Enum\_PIVOT::SET_ID
     * @covers \Drobotik\Eav\Enum\_PIVOT::GROUP_ID
     * @covers \Drobotik\Eav\Enum\_PIVOT::ATTR_ID
     */
    public function columns() {
        $this->assertEquals('pivot_id', _PIVOT::ID);
        $this->assertEquals(_DOMAIN::ID, _PIVOT::DOMAIN_ID);
        $this->assertEquals( _SET::ID, _PIVOT::SET_ID);
        $this->assertEquals(_GROUP::ID, _PIVOT::GROUP_ID);
        $this->assertEquals(_ATTR::ID, _PIVOT::ATTR_ID);
    }

}
