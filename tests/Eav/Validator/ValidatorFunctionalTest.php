<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Validator;

use Drobotik\Eav\Validation\Constraints\RequiredConstraint;
use Drobotik\Eav\Validation\Validator;
use Drobotik\Eav\Validation\Violation;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\DummyConstraintFixture;

class ValidatorFunctionalTest extends TestCase
{
    private Validator $validator;
    public function setUp(): void
    {
        parent::setUp();
        $this->validator = new Validator();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Validator::validate
     */
    public function it_validates_successfully_with_no_violations()
    {
        $value = 'test';
        $constraints = [new DummyConstraintFixture(false)];

        $result = $this->validator->validate('name', $value, $constraints);
        $this->assertNull($result);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Validator::validate
     */
    public function it_returns_violation_if_constraint_fails()
    {
        $value = 'fail';
        $constraints = [new DummyConstraintFixture(true)];

        $result = $this->validator->validate('name', $value, $constraints);
        $this->assertInstanceOf(Violation::class, $result);
        $this->assertEquals('name', $result->getField());
        $this->assertEquals("Invalid value", $result->getMessage());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Validator::validateAll
     */
    public function it_returns_no_violations_for_valid_data_in_validate_all()
    {
        $data = ['name' => 'John'];
        $rules = ['name' => [new DummyConstraintFixture(false), new RequiredConstraint()]];

        $result = $this->validator->validateAll($data, $rules);
        $this->assertEmpty($result);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Validator::validateAll
     */
    public function it_returns_violation_for_missing_required_field()
    {
        $data = [];
        $rules = ['email' => [new RequiredConstraint()]];

        $result = $this->validator->validateAll($data, $rules);

        $this->assertCount(1, $result);
        $this->assertEquals('email', $result[0]->getField());
        $this->assertEquals("The field 'email' is required.", $result[0]->getMessage());
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Validator::validateAll
     */
    public function it_skips_optional_fields_if_not_set()
    {
        $data = [];
        $rules = ['optional_field' => [new DummyConstraintFixture(true)]];

        $result = $this->validator->validateAll($data, $rules);
        $this->assertEmpty($result);
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Validator::validateAll
     */
    public function it_returns_violation_for_invalid_present_field()
    {
        $data = ['username' => 'bad'];
        $rules = ['username' => [new DummyConstraintFixture(true)]];

        $result = $this->validator->validateAll($data, $rules);

        $this->assertCount(1, $result);
        $this->assertEquals('username', $result[0]->getField());
        $this->assertEquals('Invalid value', $result[0]->getMessage());
    }
}