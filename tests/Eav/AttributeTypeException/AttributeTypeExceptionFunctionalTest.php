<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeTypeException;

use Drobotik\Eav\Exception\AttributeTypeException;
use PHPUnit\Framework\TestCase;

class AttributeTypeExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\AttributeTypeException::unsupportedType
     */
    public function unsupported_type()
    {
        $this->expectException(AttributeTypeException::class);
        $this->expectExceptionMessage(AttributeTypeException::UNSUPPORTED_TYPE);
        AttributeTypeException::unsupportedType();
    }
}
