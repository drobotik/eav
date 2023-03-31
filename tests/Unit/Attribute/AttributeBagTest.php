<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\AttributeBag;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Strategy;
use PHPUnit\Framework\TestCase;

class AttributeBagTest extends TestCase
{
    protected AttributeBag $bag;

    public function setUp(): void
    {
        parent::setUp();
        $this->bag = new AttributeBag();
    }

    /** @test */
    public function field() {
        $this->bag->setField(_ATTR::STRATEGY, 'test');
        $this->assertEquals('test', $this->bag->getField(_ATTR::STRATEGY));
        $this->bag->resetField(_ATTR::STRATEGY);
        $this->assertEquals(Strategy::class, $this->bag->getField(_ATTR::STRATEGY));
    }

    /** @test */
    public function get_fields() {
        $this->assertSame(_ATTR::bag(), $this->bag->getFields());
    }

    /** @test */
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