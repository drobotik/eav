<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesConfigValidator;

use Drobotik\Eav\Import\Attributes\ConfigValidator;
use PHPUnit\Framework\TestCase;

class ConfigValidatorFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Attributes\ConfigValidator::validate
     */
    public function validate()
    {
        $validator = $this->getMockBuilder(ConfigValidator::class)
            ->onlyMethods(['validateAttributes', 'validatePivots'])
            ->getMock();
        $validator->expects($this->once())->method('validateAttributes');
        $validator->expects($this->once())->method('validatePivots');
        $validator->validate();
    }
}