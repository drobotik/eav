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
     * @covers \Drobotik\Eav\Enum\_ENTITY::ID
     * @covers \Drobotik\Eav\Enum\_ENTITY::DOMAIN_ID
     * @covers \Drobotik\Eav\Enum\_ENTITY::ATTR_SET_ID
     * @covers \Drobotik\Eav\Enum\_ENTITY::SERVICE_KEY
     */
    public function columns() {
        $this->assertEquals('entity_id', _ENTITY::ID);
        $this->assertEquals('domain_id', _ENTITY::DOMAIN_ID);
        $this->assertEquals( _SET::ID, _ENTITY::ATTR_SET_ID);
        $this->assertEquals( 'service_key', _ENTITY::SERVICE_KEY);
    }

}
