<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSetEnum;

use Kuperwood\Eav\Enum\_DOMAIN;
use Kuperwood\Eav\Enum\_SET;
use PHPUnit\Framework\TestCase;

class AttributeSetEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_SET::table
     */
    public function table() {
        $this->assertEquals('eav_attribute_sets', _SET::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_SET::ID
     * @covers \Kuperwood\Eav\Enum\_SET::NAME
     * @covers \Kuperwood\Eav\Enum\_SET::DOMAIN_ID
     */
    public function columns() {
        $this->assertEquals('set_id', _SET::ID);
        $this->assertEquals('name', _SET::NAME);
        $this->assertEquals(_DOMAIN::ID, _SET::DOMAIN_ID);
    }

}
