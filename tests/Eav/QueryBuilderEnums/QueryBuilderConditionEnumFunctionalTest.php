<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderEnums;


use Drobotik\Eav\Enum\QB_CONDITION;
use PHPUnit\Framework\TestCase;

class QueryBuilderConditionEnumFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_CONDITION::name
     */
    public function name()
    {
        $this->assertEquals('and', QB_CONDITION::AND);
        $this->assertEquals('or', QB_CONDITION::OR);
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Enum\QB_CONDITION::getCase
     */
    public function getCase()
    {
        $this->assertEquals(QB_CONDITION::AND, QB_CONDITION::getCase(QB_CONDITION::AND));
        $this->assertEquals(QB_CONDITION::OR, QB_CONDITION::getCase(QB_CONDITION::OR));
    }
}
