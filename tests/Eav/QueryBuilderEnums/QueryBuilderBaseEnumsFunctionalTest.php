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
use Drobotik\Eav\Enum\QB_JOIN;
use PHPUnit\Framework\TestCase;

class QueryBuilderBaseEnumsFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Enum\QB_CONFIG
     */
    public function config()
    {
        $this->assertEquals("condition", QB_CONFIG::CONDITION);
        $this->assertEquals("rules", QB_CONFIG::RULES);
        $this->assertEquals("name", QB_CONFIG::NAME);
        $this->assertEquals("type", QB_CONFIG::TYPE);
        $this->assertEquals("value", QB_CONFIG::VALUE);
        $this->assertEquals("operator", QB_CONFIG::OPERATOR);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Enum\QB_JOIN
     */
    public function join()
    {
        $this->assertEquals("table", QB_JOIN::TABLE);
        $this->assertEquals("name", QB_JOIN::NAME);
        $this->assertEquals("attr_param", QB_JOIN::ATTR_PARAM);
    }
}
