<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Strategy;
use Tests\TestCase;

class AttributeEnumTest extends TestCase
{
    /** @test */
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
            _ATTR::TYPE->column() => ATTR_TYPE::STRING,
            _ATTR::STRATEGY->column() => Strategy::class,
            _ATTR::DESCRIPTION->column() => null,
        ], $cases);
    }

    /** @test */
    public function bag() {
        $cases = [];
        foreach (_ATTR::cases() as $case) {
            $cases[$case->column()] = $case->default();
        }
        $this->assertSame($cases, _ATTR::bag());
    }
}
