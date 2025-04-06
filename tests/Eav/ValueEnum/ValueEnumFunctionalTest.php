<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueEnum;

use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_DOMAIN;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Enum\_VALUE;
use PHPUnit\Framework\TestCase;

class ValueEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_VALUE::table
     */
    public function table() {
        $this->assertEquals('eav_value_%s', _VALUE::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_VALUE::ID
     * @covers \Kuperwood\Eav\Enum\_VALUE::DOMAIN_ID
     * @covers \Kuperwood\Eav\Enum\_VALUE::ENTITY_ID
     * @covers \Kuperwood\Eav\Enum\_VALUE::ATTRIBUTE_ID
     * @covers \Kuperwood\Eav\Enum\_VALUE::VALUE
     */
    public function columns() {
        $this->assertEquals('value_id', _VALUE::ID);
        $this->assertEquals( _DOMAIN::ID, _VALUE::DOMAIN_ID);
        $this->assertEquals( _ENTITY::ID, _VALUE::ENTITY_ID);
        $this->assertEquals(_ATTR::ID, _VALUE::ATTRIBUTE_ID);
        $this->assertEquals('value', _VALUE::VALUE);
    }
}
