<?php

declare(strict_types=1);

namespace Tests\Unit\AttributeBag;

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