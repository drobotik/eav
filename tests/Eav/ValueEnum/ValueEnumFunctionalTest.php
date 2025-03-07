<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueEnum;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_VALUE;
use PHPUnit\Framework\TestCase;

class ValueEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_VALUE::table
     */
    public function table() {
        $this->assertEquals('eav_value_%s', _VALUE::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_VALUE::ID
     * @covers \Drobotik\Eav\Enum\_VALUE::DOMAIN_ID
     * @covers \Drobotik\Eav\Enum\_VALUE::ENTITY_ID
     * @covers \Drobotik\Eav\Enum\_VALUE::ATTRIBUTE_ID
     * @covers \Drobotik\Eav\Enum\_VALUE::VALUE
     */
    public function columns() {
        $this->assertEquals('value_id', _VALUE::ID);
        $this->assertEquals( _DOMAIN::ID, _VALUE::DOMAIN_ID);
        $this->assertEquals( _ENTITY::ID, _VALUE::ENTITY_ID);
        $this->assertEquals(_ATTR::ID, _VALUE::ATTRIBUTE_ID);
        $this->assertEquals('value', _VALUE::VALUE);
    }
}
