<?php

declare(strict_types=1);

namespace Tests\Unit\ValueValidator;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Strategy;
use Drobotik\Eav\Value\ValueManager;
use Drobotik\Eav\Value\ValueValidator;
use PHPUnit\Framework\TestCase;

class ValueValidatorBehaviorTest extends TestCase
{
    private ValueValidator $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new ValueValidator;
    }

    /** @test */
    public function default_value_rule() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->validator->setAttributeContainer($container);
        $this->assertEquals(
            ATTR_TYPE::INTEGER->validationRule(),
            $this->validator->getDefaultValueRule()
        );
        $attribute->setType(ATTR_TYPE::TEXT->value());
        $this->assertEquals(
            ATTR_TYPE::TEXT->validationRule(),
            $this->validator->getDefaultValueRule()
        );
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
    public function validate_field_fails() {
        $result = (new Result())->validationFails();
        $result->setData(['email' => 'email is invalid']);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['validate'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('validate')
            ->willReturn($result);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getStrategy'])
            ->getMock();
        $container->expects($this->once())
            ->method('getStrategy')
            ->willReturn($strategy);
        $validator = $this->getMockBuilder(ValueValidator::class)
            ->onlyMethods(['getAttributeContainer'])
            ->getMock();
        $validator->expects($this->once())
            ->method('getAttributeContainer')
            ->willReturn($container);
        $this->assertSame($result->getData(), $validator->validateField());
    }

    /** @test */
    public function validate_field_ok() {
        $result = (new Result())->validationPassed();
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['validate'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('validate')
            ->willReturn($result);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getStrategy'])
            ->getMock();
        $container->expects($this->once())
            ->method('getStrategy')
            ->willReturn($strategy);
        $validator = $this->getMockBuilder(ValueValidator::class)
            ->onlyMethods(['getAttributeContainer'])
            ->getMock();
        $validator->expects($this->once())
            ->method('getAttributeContainer')
            ->willReturn($container);
        $this->assertNull($validator->validateField());
    }

}