<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeBag;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{

    /** @test */
    public function bag() {
        $attribute = new Attribute();
        $bag = new AttributeBag();
        $attribute->setBag(new AttributeBag());
        $this->assertEquals($bag, $attribute->getBag());
    }
}