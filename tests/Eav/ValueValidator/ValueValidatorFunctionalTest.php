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
use Drobotik\Eav\Validation\Constraints\NotBlankConstraint;
use Drobotik\Eav\Validation\Constraints\NumericConstraint;
use Drobotik\Eav\Validation\Constraints\RequiredConstraint;
use Drobotik\Eav\Validation\Validator;
use Drobotik\Eav\Value\ValueManager;
use Drobotik\Eav\Value\ValueValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
        $attribute->setType(ATTR_TYPE::INTEGER);
        $container = new AttributeContainer();
        $container->setAttribute($attribute);
        $this->validator->setAttributeContainer($container);
        $this->assertEquals(ATTR_TYPE::validationRule(ATTR_TYPE::INTEGER),$this->validator->getDefaultValueRule());
        $attribute->setType(ATTR_TYPE::TEXT);
        $this->assertEquals(ATTR_TYPE::validationRule(ATTR_TYPE::TEXT),$this->validator->getDefaultValueRule());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Value\ValueValidator::getRules
     */
    public function validation_rules() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER);
        $container = new AttributeContainer();
        $container->setAttribute($attribute)
            ->makeStrategy();
        $this->validator->setAttributeContainer($container);
        $this->assertEquals(
            [
                _VALUE::ENTITY_ID => [new RequiredConstraint(), new NotBlankConstraint(), new NumericConstraint()],
                _VALUE::DOMAIN_ID => [new RequiredConstraint(), new NotBlankConstraint(),  new NumericConstraint()],
                _VALUE::ATTRIBUTE_ID => [new RequiredConstraint(), new NotBlankConstraint(),  new NumericConstraint()],
                _VALUE::VALUE => $this->validator->getDefaultValueRule(),
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
        $attribute->setType(ATTR_TYPE::INTEGER);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['rules'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('rules')
            ->willReturn([new NotBlankConstraint()]);
        $container = new AttributeContainer();
        $container->setAttribute($attribute)
            ->setStrategy($strategy);
        $this->validator->setAttributeContainer($container);
        $result = $this->validator->getRules();
        $valueRules = $result[_VALUE::VALUE];
        $this->assertCount(1, $valueRules);
        $this->assertInstanceOf(NotBlankConstraint::class,  $valueRules[0]);
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
                _VALUE::ENTITY_ID => $entity->getKey(),
                _VALUE::DOMAIN_ID => $entity->getDomainKey(),
                _VALUE::ATTRIBUTE_ID => $attribute->getKey(),
                _VALUE::VALUE => $valueManager->getRuntime()
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
        $attribute->setType(ATTR_TYPE::STRING);
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
        $this->assertInstanceOf(Validator::class, $validator);
    }
}