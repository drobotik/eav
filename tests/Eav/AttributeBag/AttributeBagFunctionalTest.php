<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeBag;

use Drobotik\Eav\AttributeBag;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Strategy;
use PHPUnit\Framework\TestCase;

class AttributeBagFunctionalTest extends TestCase
{
    protected AttributeBag $bag;

    public function setUp(): void
    {
        parent::setUp();
        $this->bag = new AttributeBag();
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeBag::setField, \Drobotik\Eav\AttributeBag::getField, \Drobotik\Eav\AttributeBag::resetField
     */
    public function field() {
        $this->bag->setField(_ATTR::STRATEGY, 'test');
        $this->assertEquals('test', $this->bag->getField(_ATTR::STRATEGY));
        $this->bag->resetField(_ATTR::STRATEGY);
        $this->assertEquals(Strategy::class, $this->bag->getField(_ATTR::STRATEGY));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeBag::getFields
     */
    public function get_fields() {
        $this->assertSame(_ATTR::bag(), $this->bag->getFields());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeBag::setFields, \Drobotik\Eav\AttributeBag::getFields
     */
    public function set_fields() {
        $input = [
            _ATTR::ID->column() => 123,
            _ATTR::STRATEGY->column() => 'test',
        ];
        $result = $this->bag->setFields($input);
        $this->assertSame($this->bag, $result);
        $this->assertEquals($input, $this->bag->getFields());
    }
}