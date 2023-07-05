<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */

namespace Tests\Eav\Validation;

use Drobotik\Eav\Validation\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class AssertFunctionalTest extends TestCase
{
    private function checkPayload($expected, $value, $rule)
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($value, [$rule]);
        $this->assertEquals($expected, count($violations), $value);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Validation\Assert::integer
     */
    public function integer()
    {
        $this->checkPayload(0, 10, Assert::integer());
        $this->checkPayload(0, 0, Assert::integer());
        $this->checkPayload(1, -5, Assert::integer());
        $this->checkPayload(1, 10.5, Assert::integer());
        $this->checkPayload(1, '10', Assert::integer());
        $this->checkPayload(1, 'abc', Assert::integer());
    }
}