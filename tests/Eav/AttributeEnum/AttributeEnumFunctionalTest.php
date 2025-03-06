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
     * @covers \Drobotik\Eav\Enum\_ATTR::ID
     * @covers \Drobotik\Eav\Enum\_ATTR::NAME
     * @covers \Drobotik\Eav\Enum\_ATTR::DOMAIN_ID
     * @covers \Drobotik\Eav\Enum\_ATTR::SOURCE
     * @covers \Drobotik\Eav\Enum\_ATTR::DEFAULT_VALUE
     * @covers \Drobotik\Eav\Enum\_ATTR::TYPE
     * @covers \Drobotik\Eav\Enum\_ATTR::STRATEGY
     * @covers \Drobotik\Eav\Enum\_ATTR::DESCRIPTION
     */
    public function columns() {
        $this->assertEquals('attribute_id', _ATTR::ID);
        $this->assertEquals('name', _ATTR::NAME);
        $this->assertEquals(_DOMAIN::ID, _ATTR::DOMAIN_ID);
        $this->assertEquals('source', _ATTR::SOURCE);
        $this->assertEquals('default_value', _ATTR::DEFAULT_VALUE);
        $this->assertEquals('type', _ATTR::TYPE);
        $this->assertEquals('strategy', _ATTR::STRATEGY);
        $this->assertEquals('description', _ATTR::DESCRIPTION);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\_ATTR::bag
     */
    public function bag() {
        $this->assertEquals(null, _ATTR::bag(_ATTR::ID));
        $this->assertEquals(null, _ATTR::bag(_ATTR::NAME));
        $this->assertEquals(null, _ATTR::bag(_ATTR::DOMAIN_ID));
        $this->assertEquals(null, _ATTR::bag(_ATTR::SOURCE));
        $this->assertEquals(null, _ATTR::bag(_ATTR::DEFAULT_VALUE));
        $this->assertEquals(null, _ATTR::bag(_ATTR::DESCRIPTION));
        $this->assertEquals(ATTR_TYPE::STRING->value(), _ATTR::bag(_ATTR::TYPE));
        $this->assertEquals( Strategy::class, _ATTR::bag(_ATTR::STRATEGY));
    }
}
