<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityEnum;

use Drobotik\Eav\Enum\_DOMAIN;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_SET;
use PHPUnit\Framework\TestCase;

class EntityEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_ENTITY::table
     */
    public function table() {
        $this->assertEquals('eav_entities', _ENTITY::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_ENTITY::column
     */
    public function columns() {
        $cases = [];
        foreach (_ENTITY::cases() as $case) {
            $cases[$case->column()] = $case->column();
        }
        $this->assertEquals([
            _ENTITY::ID->column() => 'entity_id',
            _ENTITY::DOMAIN_ID->column() => _DOMAIN::ID->column(),
            _ENTITY::ATTR_SET_ID->column() => _SET::ID->column(),
            _ENTITY::SERVICE_KEY->column() => 'service_key'
        ], $cases);
    }

}
