<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderAttributes;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use PHPUnit\Framework\TestCase;

class QueryBuilderAttributesFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderAttributes::appendAttribute
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderAttributes::getAttribute
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderAttributes::isAttribute
     */
    public function attribute()
    {
        $attributes = new QueryBuilderAttributes();
        $this->assertFalse($attributes->isAttribute('test'));
        $attribute = [_ATTR::NAME->column() => 'test'];
        $attributes->appendAttribute($attribute);
        $this->assertTrue($attributes->isAttribute('test'));
        $this->assertSame($attribute, $attributes->getAttribute('test'));
    }
}