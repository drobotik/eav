<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeBag;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Source;
use Kuperwood\Eav\Strategy;
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

    public function strategy() {
        $attribute = new Attribute();
        $strategy = new Strategy();
        $attribute->setStrategy($strategy);
        $this->assertEquals($strategy, $attribute->getStrategy());
    }

    public function attributeSet() {
        $attribute = new Attribute();
        $attrSet = new AttributeSet();
        $attribute->setAttributeSet($attrSet);
        $this->assertEquals($attrSet, $attribute->getAttributeSet());
    }

    public function source() {
        $attribute = new Attribute();
        $source = new Source();
        $attribute->setSource($source);
        $this->assertEquals($source, $attribute->getSource());
    }
}