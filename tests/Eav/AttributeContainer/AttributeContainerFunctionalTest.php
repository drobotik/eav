<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeContainer;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\AttributeSetAction;
use Drobotik\Eav\Strategy;
use Drobotik\Eav\Value\ValueAction;
use Drobotik\Eav\Value\ValueManager;
use Drobotik\Eav\Value\ValueValidator;
use Exception;
use PHPUnit\Framework\TestCase;
use SplFileObject;

class AttributeContainerFunctionalTest extends TestCase
{
    private AttributeContainer $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new AttributeContainer();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function make_exception() {

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('not supported');
        $this->container->make(SplFileObject::class);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::setAttributeSet
     * @covers \Drobotik\Eav\AttributeContainer::getAttributeSet
     */
    public function attribute_set() {
        $attributeSet = new AttributeSet();
        $result = $this->container->setAttributeSet($attributeSet);
        $this->assertSame($result, $this->container);
        $this->assertSame($attributeSet , $this->container->getAttributeSet());
        $this->assertSame($this->container, $this->container->getAttributeSet()->getAttributeContainer());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeAttributeSet
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeAttributeSet() {
        $instance = $this->container->make(AttributeSet::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(AttributeSet::class, $instance);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeAttributeSet
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeAttributeSetAlias() {
        $result = $this->container->makeAttributeSet();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(AttributeSet::class, $this->container->getAttributeSet());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::setAttributeSetAction
     * @covers \Drobotik\Eav\AttributeContainer::getAttributeSetAction
     */
    public function attribute_set_action() {
        $attrSetAction = new AttributeSetAction();
        $result = $this->container->setAttributeSetAction($attrSetAction);
        $this->assertSame($result, $this->container);
        $this->assertSame($attrSetAction, $this->container->getAttributeSetAction());
        $this->assertSame($this->container, $this->container->getAttributeSetAction()->getAttributeContainer());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeAttributeSetAction
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeAttributeSetAction() {
        $instance = $this->container->make(AttributeSetAction::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(AttributeSetAction::class, $instance);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeAttributeSetAction
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeAttributeSetActionAlias() {
        $result = $this->container->makeAttributeSetAction();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(AttributeSetAction::class, $this->container->getAttributeSetAction());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::setAttribute
     * @covers \Drobotik\Eav\AttributeContainer::getAttribute
     */
    public function attribute() {
        $attribute = new Attribute();
        $result = $this->container->setAttribute($attribute);
        $this->assertSame($result, $this->container);
        $this->assertSame($attribute, $this->container->getAttribute());
        $this->assertSame($this->container, $this->container->getAttribute()->getAttributeContainer());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeAttribute
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeAttribute() {
        $instance = $this->container->make(Attribute::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(Attribute::class, $instance);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeAttribute
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeAttributeAlias() {
        $result = $this->container->makeAttribute();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(Attribute::class, $this->container->getAttribute());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::setStrategy
     * @covers \Drobotik\Eav\AttributeContainer::getStrategy
     */
    public function strategy() {
        $strategy = new Strategy();
        $result = $this->container->setStrategy($strategy);
        $this->assertSame($result, $this->container);
        $this->assertSame($strategy, $this->container->getStrategy());
        $this->assertSame($this->container, $this->container->getStrategy()->getAttributeContainer());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeStrategy
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeStrategy() {
        $instance = $this->container->make(Strategy::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(Strategy::class, $instance);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeStrategy
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeStrategyAlias() {
        $result = $this->container->makeStrategy();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(Strategy::class, $this->container->getStrategy());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::setValueManager
     * @covers \Drobotik\Eav\AttributeContainer::getValueManager
     */
    public function value_manager() {
        $valueManager = new ValueManager();
        $result = $this->container->setValueManager($valueManager);
        $this->assertSame($result, $this->container);
        $this->assertSame($valueManager, $this->container->getValueManager());
        $this->assertSame($this->container, $this->container->getValueManager()->getAttributeContainer());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeValueManager
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeValueManager() {
        $instance = $this->container->make(ValueManager::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(ValueManager::class, $instance);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeValueManager
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeValueManagerAlias() {
        $result = $this->container->makeValueManager();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(ValueManager::class, $this->container->getValueManager());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::setValueValidator
     * @covers \Drobotik\Eav\AttributeContainer::getValueValidator
     */
    public function value_validator() {
        $valueValidator = new ValueValidator();
        $result = $this->container->setValueValidator($valueValidator);
        $this->assertSame($result, $this->container);
        $this->assertSame($valueValidator, $this->container->getValueValidator());
        $this->assertSame($this->container, $this->container->getValueValidator()->getAttributeContainer());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeValueValidator
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeValueValidator() {
        $instance = $this->container->make(ValueValidator::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(ValueValidator::class, $instance);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeValueValidator
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeValueValidatorAlias() {
        $result = $this->container->makeValueValidator();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(ValueValidator::class, $this->container->getValueValidator());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::getValueAction
     * @covers \Drobotik\Eav\AttributeContainer::setValueAction
     */
    public function value_action() {
        $valueAction = new ValueAction();
        $result = $this->container->setValueAction($valueAction);
        $this->assertSame($result, $this->container);
        $this->assertSame($valueAction, $this->container->getValueAction());
        $this->assertSame($this->container, $this->container->getValueAction()->getAttributeContainer());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeValueAction
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeValueAction() {
        $instance = $this->container->make(ValueAction::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(ValueAction::class, $instance);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeContainer::makeValueAction
     * @covers \Drobotik\Eav\AttributeContainer::make
     */
    public function makeValueActionAlias() {
        $result = $this->container->makeValueAction();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(ValueAction::class, $this->container->getValueAction());
    }

}