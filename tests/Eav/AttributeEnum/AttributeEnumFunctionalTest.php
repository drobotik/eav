<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeEnum;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Strategy;
use PHPUnit\Framework\TestCase;

class AttributeEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_ATTR::table
     */
    public function table() {
        $this->assertEquals('eav_attributes', _ATTR::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_ATTR::column
     */
    public function columns() {
        $cases = [];
        foreach (_ATTR::cases() as $case) {
            $cases[$case->column()] = $case->column();
        }
        $this->assertEquals([
            _ATTR::ID->column() => 'attribute_id',
            _ATTR::NAME->column() => 'name',
            _ATTR::DOMAIN_ID->column() => _DOMAIN::ID->column(),
            _ATTR::SOURCE->column() => 'source',
            _ATTR::DEFAULT_VALUE->column() => 'default_value',
            _ATTR::TYPE->column() => 'type',
            _ATTR::STRATEGY->column() => 'strategy',
            _ATTR::DESCRIPTION->column() => 'description',
        ], $cases);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_ATTR::default
     */
    public function default() {
        $cases = [];
        foreach (_ATTR::cases() as $case) {
            $cases[$case->column()] = $case->default();
        }
        $this->assertEquals([
            _ATTR::ID->column() => null,
            _ATTR::NAME->column() => null,
            _ATTR::DOMAIN_ID->column() => null,
            _ATTR::SOURCE->column() => null,
            _ATTR::DEFAULT_VALUE->column() => null,
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value(),
            _ATTR::STRATEGY->column() => Strategy::class,
            _ATTR::DESCRIPTION->column() => null,
        ], $cases);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_ATTR::bag
     */
    public function bag() {
        $cases = [];
        foreach (_ATTR::cases() as $case) {
            $cases[$case->column()] = $case->default();
        }
        $this->assertSame($cases, _ATTR::bag());
    }
}
