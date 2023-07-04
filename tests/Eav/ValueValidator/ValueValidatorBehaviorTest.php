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
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Strategy;
use Drobotik\Eav\Value\ValueValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;

class ValueValidatorBehaviorTest extends TestCase
{
    private ValueValidator $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new ValueValidator;
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Value\ValueValidator::getRules
     */
    public function validation_rules_with_custom_rule() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER->value());
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['rules'])
            ->getMock();
        $collection = [
            new Constraints\Regex('/nd/')
        ];
        $strategy->expects($this->once())
            ->method('rules')
            ->willReturn($collection);
        $container = new AttributeContainer();
        $container->setAttribute($attribute)
            ->setStrategy($strategy);
        $this->validator->setAttributeContainer($container);
        $result = $this->validator->getRules();
        /** @var \Symfony\Component\Validator\Constraints\Required  $t */
        $t = $result->fields[_VALUE::VALUE->column()];
        $this->assertEquals($collection, $t->getNestedConstraints());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Value\ValueValidator::validateField
     */
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
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Value\ValueValidator::validateField
     */
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