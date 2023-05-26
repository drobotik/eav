<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeException;

use Drobotik\Eav\Exception\AttributeException;
use PHPUnit\Framework\TestCase;

class AttributeExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\AttributeException::undefinedAttributeName
     */
    public function undefined_name()
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(AttributeException::UNDEFINED_NAME);
        AttributeException::undefinedAttributeName();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\AttributeException::undefinedAttributeType
     */
    public function undefined_type()
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(AttributeException::UNDEFINED_TYPE);
        AttributeException::undefinedAttributeType();
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\AttributeException::unsupportedType
     */
    public function unsupported_type()
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNSUPPORTED_TYPE, 'test'));
        AttributeException::unsupportedType('test');
    }
}