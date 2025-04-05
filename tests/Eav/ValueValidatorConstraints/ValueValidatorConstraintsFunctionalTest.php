<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueValidatorConstraints;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Strategy;
use Drobotik\Eav\Validation\Constraints\DateConstraint;
use Drobotik\Eav\Validation\Constraints\LengthConstraint;
use Drobotik\Eav\Validation\Constraints\NotBlankConstraint;
use Drobotik\Eav\Validation\Constraints\NotNullConstraint;
use Drobotik\Eav\Validation\Constraints\NumericConstraint;
use Drobotik\Eav\Validation\Constraints\RegexConstraint;
use Drobotik\Eav\Validation\Constraints\RequiredConstraint;
use Drobotik\Eav\Validation\Validator;
use Drobotik\Eav\Value\ValueManager;
use Drobotik\Eav\Value\ValueValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValueValidatorConstraintsFunctionalTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Constraints\DateConstraint::validate
     */
    public function date_constraint() {
        $constraint = new DateConstraint();
        $this->assertEquals('This value must be a string.', $constraint->validate(123));
        $this->assertEquals('This value must be a valid date in Y-m-d format.', $constraint->validate('01.04.2020'));
        $constraint = new DateConstraint('d.m.Y H:i:s');
        $this->assertEquals('This value must be a valid date in d.m.Y H:i:s format.', $constraint->validate('01-04-2020'));
        $this->assertNull($constraint->validate('01.04.2020 12:34:15'));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Constraints\LengthConstraint::validate
     */
    public function length_constraint() {
        $constraint = new LengthConstraint(1, 5);
        $this->assertEquals('This value must be a string.', $constraint->validate(123));
        $this->assertEquals('This value must be between 1 and 5 characters long.', $constraint->validate('qwertyui'));
        $this->assertEquals('This value must be between 1 and 5 characters long.', $constraint->validate(''));
        $this->assertNull($constraint->validate('qwert'));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Constraints\NotBlankConstraint::validate
     */
    public function not_blank_constraint() {
        $constraint = new NotBlankConstraint();
        $this->assertEquals('This value cannot be blank.', $constraint->validate(' '));
        $this->assertNull($constraint->validate(123));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Constraints\NotBlankConstraint::validate
     */
    public function not_null_constraint() {
        $constraint = new NotNullConstraint();
        $this->assertEquals('This value cannot be null.', $constraint->validate(null));
        $this->assertNull($constraint->validate(123));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Constraints\NumericConstraint::validate
     */
    public function numeric_constraint() {
        $constraint = new NumericConstraint();
        $this->assertEquals('This value must be a number.', $constraint->validate('123d'));
        $this->assertNull($constraint->validate('123'));
        $this->assertNull($constraint->validate(123));
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Constraints\RegexConstraint::validate
     */
    public function regex_constraint() {
        // Match a simple email pattern
        $pattern = '/^[\w\.-]+@[\w\.-]+\.\w+$/';
        $constraint = new RegexConstraint($pattern);

        // Valid email
        $this->assertNull(
            $constraint->validate('test@example.com'),
            'Valid email should return null'
        );

        // Invalid email
        $this->assertEquals(
            'This value does not match the required pattern.',
            $constraint->validate('invalid-email'),
            'Invalid email should return error message'
        );

        // Custom error message
        $customMessage = 'Email format is incorrect.';
        $constraintWithCustomMessage = new RegexConstraint($pattern, $customMessage);
        $this->assertEquals(
            $customMessage,
            $constraintWithCustomMessage->validate('invalid-email'),
            'Invalid email should return custom error message'
        );
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Constraints\RequiredConstraint::validate
     */
    public function required_constraint() {
        $constraint = new RequiredConstraint();

        // Null value should fail
        $this->assertEquals(
            'This field is required.',
            $constraint->validate(null),
            'Null value should return required message.'
        );

        // Empty string should fail
        $this->assertEquals(
            'This field is required.',
            $constraint->validate(''),
            'Empty string should return required message.'
        );

        // Whitespace is allowed (non-empty)
        $this->assertNull(
            $constraint->validate(' '),
            'Whitespace should be considered non-empty.'
        );

        // Valid value
        $this->assertNull(
            $constraint->validate('valid input'),
            'Non-empty value should pass validation.'
        );

        // Numeric zero should be valid
        $this->assertNull(
            $constraint->validate(0),
            'Numeric zero should be considered present.'
        );

        // String zero should also be valid
        $this->assertNull(
            $constraint->validate('0'),
            'String "0" should be considered present.'
        );
    }
}