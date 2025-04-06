<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeException;

use Kuperwood\Eav\Exception\AttributeException;
use PHPUnit\Framework\TestCase;

class AttributeExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Exception\AttributeException::undefinedAttributeName
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
     * @covers \Kuperwood\Eav\Exception\AttributeException::undefinedAttributeType
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
     * @covers \Kuperwood\Eav\Exception\AttributeException::unsupportedType
     */
    public function unsupported_type()
    {
        $this->expectException(AttributeException::class);
        $this->expectExceptionMessage(sprintf(AttributeException::UNSUPPORTED_TYPE, 'test'));
        AttributeException::unsupportedType('test');
    }
}