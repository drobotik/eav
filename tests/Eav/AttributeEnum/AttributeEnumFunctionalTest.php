<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeEnum;

use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_DOMAIN;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Strategy;
use PHPUnit\Framework\TestCase;

class AttributeEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_ATTR::table
     */
    public function table() {
        $this->assertEquals('eav_attributes', _ATTR::table());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Enum\_ATTR::ID
     * @covers \Kuperwood\Eav\Enum\_ATTR::NAME
     * @covers \Kuperwood\Eav\Enum\_ATTR::DOMAIN_ID
     * @covers \Kuperwood\Eav\Enum\_ATTR::SOURCE
     * @covers \Kuperwood\Eav\Enum\_ATTR::DEFAULT_VALUE
     * @covers \Kuperwood\Eav\Enum\_ATTR::TYPE
     * @covers \Kuperwood\Eav\Enum\_ATTR::STRATEGY
     * @covers \Kuperwood\Eav\Enum\_ATTR::DESCRIPTION
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
     * @covers \Kuperwood\Eav\Enum\_ATTR::bag
     */
    public function bag() {
        $this->assertEquals(null, _ATTR::bag(_ATTR::ID));
        $this->assertEquals(null, _ATTR::bag(_ATTR::NAME));
        $this->assertEquals(null, _ATTR::bag(_ATTR::DOMAIN_ID));
        $this->assertEquals(null, _ATTR::bag(_ATTR::SOURCE));
        $this->assertEquals(null, _ATTR::bag(_ATTR::DEFAULT_VALUE));
        $this->assertEquals(null, _ATTR::bag(_ATTR::DESCRIPTION));
        $this->assertEquals(ATTR_TYPE::STRING, _ATTR::bag(_ATTR::TYPE));
        $this->assertEquals( Strategy::class, _ATTR::bag(_ATTR::STRATEGY));
    }
}
