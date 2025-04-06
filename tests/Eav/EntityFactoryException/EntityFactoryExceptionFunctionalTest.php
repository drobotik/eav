<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityFactoryException;

use Kuperwood\Eav\Exception\EntityFactoryException;
use PHPUnit\Framework\TestCase;

class EntityFactoryExceptionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Exception\EntityFactoryException::undefinedAttributeArray
     */
    public function attribute_array_not_provided()
    {
        $this->expectException(EntityFactoryException::class);
        $this->expectExceptionMessage(EntityFactoryException::UNDEFINED_ATTRIBUTE_ARRAY);
        EntityFactoryException::undefinedAttributeArray();
    }

}
