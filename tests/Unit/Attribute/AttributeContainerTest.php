<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\AttributeSetAction;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\Value\ValueValidator;
use Kuperwood\Eav\ValueManager;
use Tests\TestCase;

class AttributeContainerTest extends TestCase
{
    private AttributeContainer $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new AttributeContainer();
    }

    /** @test */
    public function attribute_set() {
        $attributeSet = new AttributeSet();
        $result = $this->container->setAttributeSet($attributeSet);
        $this->assertSame($result, $this->container);
        $this->assertSame($attributeSet , $this->container->getAttributeSet());
        $this->assertSame($this->container, $this->container->getAttributeSet()->getAttributeContainer());
    }

    /** @test */
    public function makeAttributeSet() {
        $instance = $this->container->make(AttributeSet::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(AttributeSet::class, $instance);
    }

    /** @test */
    public function makeAttributeSetAlias() {
        $result = $this->container->makeAttributeSet();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(AttributeSet::class, $this->container->getAttributeSet());
    }

    /** @test */
    public function attribute_set_action() {
        $attrSetAction = new AttributeSetAction();
        $result = $this->container->setAttributeSetAction($attrSetAction);
        $this->assertSame($result, $this->container);
        $this->assertSame($attrSetAction, $this->container->getAttributeSetAction());
        $this->assertSame($this->container, $this->container->getAttributeSetAction()->getAttributeContainer());
    }

    /** @test */
    public function makeAttributeSetAction() {
        $instance = $this->container->make(AttributeSetAction::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(AttributeSetAction::class, $instance);
    }

    public function makeAttributeSetActionAlias() {
        $result = $this->container->makeAttributeSetAction();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(AttributeSet::class, $this->container->getAttributeSetAction());
    }

    /** @test */
    public function attribute() {
        $attribute = new Attribute();
        $result = $this->container->setAttribute($attribute);
        $this->assertSame($result, $this->container);
        $this->assertSame($attribute, $this->container->getAttribute());
        $this->assertSame($this->container, $this->container->getAttribute()->getAttributeContainer());
    }

    /** @test */
    public function makeAttribute() {
        $instance = $this->container->make(Attribute::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(Attribute::class, $instance);
    }

    /** @test */
    public function makeAttributeAlias() {
        $result = $this->container->makeAttribute();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(Attribute::class, $this->container->getAttribute());
    }

    /** @test */
    public function strategy() {
        $strategy = new Strategy();
        $result = $this->container->setStrategy($strategy);
        $this->assertSame($result, $this->container);
        $this->assertSame($strategy, $this->container->getStrategy());
        $this->assertSame($this->container, $this->container->getStrategy()->getAttributeContainer());
    }

    /** @test */
    public function makeStrategy() {
        $instance = $this->container->make(Strategy::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(Strategy::class, $instance);
    }

    /** @test */
    public function makeStrategyAlias() {
        $result = $this->container->makeStrategy();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(Strategy::class, $this->container->getStrategy());
    }

    /** @test */
    public function value_manager() {
        $valueManager = new ValueManager();
        $result = $this->container->setValueManager($valueManager);
        $this->assertSame($result, $this->container);
        $this->assertSame($valueManager, $this->container->getValueManager());
        $this->assertSame($this->container, $this->container->getValueManager()->getAttributeContainer());
    }

    /** @test */
    public function makeValueManager() {
        $instance = $this->container->make(ValueManager::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(ValueManager::class, $instance);
    }

    /** @test */
    public function makeValueManagerAlias() {
        $result = $this->container->makeValueManager();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(ValueManager::class, $this->container->getValueManager());
    }

    /** @test */
    public function value_validator() {
        $valueValidator = new ValueValidator();
        $result = $this->container->setValueValidator($valueValidator);
        $this->assertSame($result, $this->container);
        $this->assertSame($valueValidator, $this->container->getValueValidator());
        $this->assertSame($this->container, $this->container->getValueValidator()->getAttributeContainer());
    }

    /** @test */
    public function makeValueValidator() {
        $instance = $this->container->make(ValueValidator::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(ValueValidator::class, $instance);
    }

    /** @test */
    public function makeValueValidatorAlias() {
        $result = $this->container->makeValueValidator();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(ValueValidator::class, $this->container->getValueValidator());
    }
}