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
     * @covers \Drobotik\Eav\Enum\_GROUP::ID
     * @covers \Drobotik\Eav\Enum\_GROUP::NAME
     * @covers \Drobotik\Eav\Enum\_GROUP::SET_ID
     */
    public function columns() {
        $this->assertEquals('group_id', _GROUP::ID);
        $this->assertEquals('name', _GROUP::NAME);
        $this->assertEquals(_SET::ID, _GROUP::SET_ID);
    }

}
