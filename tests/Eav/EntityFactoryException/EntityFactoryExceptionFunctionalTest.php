<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityFactoryException;

use Drobotik\Eav\Exception\EntityFactoryException;
use PHPUnit\Framework\TestCase;

class EntityFactoryExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Exception\EntityFactoryException::undefinedAttributeArray
     */
    public function attribute_array_not_provided()
    {
        $this->expectException(EntityFactoryException::class);
        $this->expectExceptionMessage(EntityFactoryException::UNDEFINED_ATTRIBUTE_ARRAY);
        EntityFactoryException::undefinedAttributeArray();
    }

}
