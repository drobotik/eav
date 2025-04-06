<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\GroupEnum;

use Kuperwood\Eav\Enum\_GROUP;
use Kuperwood\Eav\Enum\_SET;
use PHPUnit\Framework\TestCase;

class GroupEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_GROUP::table
     */
    public function table() {
        $this->assertEquals('eav_attribute_groups', _GROUP::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_GROUP::ID
     * @covers \Kuperwood\Eav\Enum\_GROUP::NAME
     * @covers \Kuperwood\Eav\Enum\_GROUP::SET_ID
     */
    public function columns() {
        $this->assertEquals('group_id', _GROUP::ID);
        $this->assertEquals('name', _GROUP::NAME);
        $this->assertEquals(_SET::ID, _GROUP::SET_ID);
    }

}
