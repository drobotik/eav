<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueValidator;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\Validation\Constraints\RegexConstraint;
use Kuperwood\Eav\Value\ValueValidator;
use PHPUnit\Framework\TestCase;

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
     * @covers \Kuperwood\Eav\Value\ValueValidator::getRules
     */
    public function validation_rules_with_custom_rule() {
        $attribute = new Attribute();
        $attribute->setType(ATTR_TYPE::INTEGER);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['rules'])
            ->getMock();
        $collection = [
            new RegexConstraint('/nd/')
        ];
        $strategy->expects($this->once())
            ->method('rules')
            ->willReturn($collection);
        $container = new AttributeContainer();
        $container->setAttribute($attribute)
            ->setStrategy($strategy);
        $this->validator->setAttributeContainer($container);
        $result = $this->validator->getRules();
        $this->assertSame($collection, $result[_VALUE::VALUE]);
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\Value\ValueValidator::validateField
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
     * @covers \Kuperwood\Eav\Value\ValueValidator::validateField
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