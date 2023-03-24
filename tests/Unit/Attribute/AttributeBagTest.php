<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\AttributeBag;
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Strategy;
use PHPUnit\Framework\TestCase;

class AttributeBagTest extends TestCase
{
    /** @test */
    public function field() {
        $bag = new AttributeBag();
        $bag->setField(_ATTR::STRATEGY, 'test');
        $this->assertEquals('test', $bag->getField(_ATTR::STRATEGY));
        $bag->resetField(_ATTR::STRATEGY);
        $this->assertEquals(Strategy::class, $bag->getField(_ATTR::STRATEGY));
    }

    /** @test */
    public function fields() {
        $bag = new AttributeBag();
        $this->assertSame(_ATTR::bag(), $bag->getFields());
    }
}