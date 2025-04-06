<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\PivotEnum;

use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_DOMAIN;
use Kuperwood\Eav\Enum\_GROUP;
use Kuperwood\Eav\Enum\_PIVOT;
use Kuperwood\Eav\Enum\_SET;
use PHPUnit\Framework\TestCase;

class PivotEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_PIVOT::table
     */
    public function table() {
        $this->assertEquals('eav_pivot', _PIVOT::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_PIVOT::ID
     * @covers \Kuperwood\Eav\Enum\_PIVOT::DOMAIN_ID
     * @covers \Kuperwood\Eav\Enum\_PIVOT::SET_ID
     * @covers \Kuperwood\Eav\Enum\_PIVOT::GROUP_ID
     * @covers \Kuperwood\Eav\Enum\_PIVOT::ATTR_ID
     */
    public function columns() {
        $this->assertEquals('pivot_id', _PIVOT::ID);
        $this->assertEquals(_DOMAIN::ID, _PIVOT::DOMAIN_ID);
        $this->assertEquals( _SET::ID, _PIVOT::SET_ID);
        $this->assertEquals(_GROUP::ID, _PIVOT::GROUP_ID);
        $this->assertEquals(_ATTR::ID, _PIVOT::ATTR_ID);
    }

}
