<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderEnums;

use Drobotik\Eav\Enum\QB_CONFIG;
use PHPUnit\Framework\TestCase;

class QueryBuilderConfigEnumFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Enum\QB_CONFIG::sql
     */
    public function sql()
    {
        $this->assertEquals("condition", QB_CONFIG::CONDITION);
        $this->assertEquals("rules", QB_CONFIG::RULES);
        $this->assertEquals("name", QB_CONFIG::NAME);
        $this->assertEquals("type", QB_CONFIG::TYPE);
        $this->assertEquals("value", QB_CONFIG::VALUE);
        $this->assertEquals("operator", QB_CONFIG::OPERATOR);
    }
}
