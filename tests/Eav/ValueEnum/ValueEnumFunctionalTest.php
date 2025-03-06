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
     * @covers \Drobotik\Eav\Enum\_VALUE::column
     */
    public function columns() {
        $cases = [];
        foreach (_VALUE::cases() as $case) {
            $cases[$case->column()] = $case->column();
        }
        $this->assertEquals([
            _VALUE::ID->column() => 'value_id',
            _VALUE::DOMAIN_ID->column() => _DOMAIN::ID,
            _VALUE::ENTITY_ID->column() => _ENTITY::ID,
            _VALUE::ATTRIBUTE_ID->column() => _ATTR::ID,
            _VALUE::VALUE->column() => 'value',
        ], $cases);
    }
}
