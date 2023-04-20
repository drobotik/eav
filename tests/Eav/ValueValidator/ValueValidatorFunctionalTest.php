<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueValidator;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Strategy;
use Drobotik\Eav\Value\ValueManager;
use Drobotik\Eav\Value\ValueValidator;
use PHPUnit\Framework\TestCase;

class ValueValidatorFunctionalTest extends TestCase
{
    private ValueValidator $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new ValueValidator;
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueValidator::getDefaultValueRule
     */
    public function default_value_rule() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->validator->setAttributeContainer($container);
        $this->assertEquals(ATTR_TYPE::INTEGER->validationRule(),$this->validator->getDefaultValueRule());
        $attribute->setType(ATTR_TYPE::TEXT->value());
        $this->assertEquals(ATTR_TYPE::TEXT->validationRule(),$this->validator->getDefaultValueRule());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueValidator::getRules
     */
    public function validation_rules() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $container = new AttributeContainer();
        $container->setAttribute($attribute)
            ->makeStrategy();
        $this->validator->setAttributeContainer($container);
        $this->assertEquals(
            [
                _VALUE::ENTITY_ID->column() => ['required', 'integer'],
                _VALUE::DOMAIN_ID->column() => ['required','integer'],
                _VALUE::ATTRIBUTE_ID->column() => ['required','integer'],
                _VALUE::VALUE->column() => $this->validator->getDefaultValueRule()
            ],
            $this->validator->getRules()
        );
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueValidator::getRules
     */
    public function validation_rules_with_custom_rule() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['rules'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('rules')
            ->willReturn(['new_rule']);
        $container = new AttributeContainer();
        $container->setAttribute($attribute)
            ->setStrategy($strategy);
        $this->validator->setAttributeContainer($container);
        $result = $this->validator->getRules();
        $this->assertEquals(['new_rule'], $result[_VALUE::VALUE->column()]);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueValidator::getValidatedData
     */
    public function validation_data() {
        $entity = new Entity();
        $entity->setDomainKey(4);
        $entity->setKey(3);
        $attrSet = new AttributeSet();
        $attrSet->setKey(2);
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->setKey(1);
        $valueManager = new ValueManager();
        $valueManager->setRuntime('test');
        $container = new AttributeContainer();
        $container
            ->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->setValueManager($valueManager);
        $this->validator->setAttributeContainer($container);
        $this->assertEquals(
            [
                _VALUE::ENTITY_ID->column() => $entity->getKey(),
                _VALUE::DOMAIN_ID->column() => $entity->getDomainKey(),
                _VALUE::ATTRIBUTE_ID->column() => $attribute->getKey(),
                _VALUE::VALUE->column() => $valueManager->getRuntime()
            ],
            $this->validator->getValidatedData()
        );
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueValidator::getValidator
     */
    public function validator() {
        $entity = new Entity();
        $entity->setDomainKey(4);
        $entity->setKey(3);
        $attrSet = new AttributeSet();
        $attrSet->setKey(2);
        $attrSet->setEntity($entity);
        $attribute = new Attribute();
        $attribute->setKey(1);
        $attribute->setType(ATTR_TYPE::STRING->value());
        $valueManager = new ValueManager();
        $valueManager->setRuntime('test');
        $container = new AttributeContainer();
        $container
            ->setAttributeSet($attrSet)
            ->setAttribute($attribute)
            ->makeStrategy()
            ->setValueManager($valueManager);
        $this->validator->setAttributeContainer($container);
        $validator = $this->validator->getValidator();
        $this->assertEquals($this->validator->getRules(), $validator->getRules());
        $this->assertEquals($this->validator->getValidatedData(), $validator->getData());
    }
}