<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EavFactoryResult;

use Drobotik\Eav\Enum\_ATTR;
use PHPUnit\Framework\TestCase;
use Drobotik\Eav\Result\EntityFactoryResult;

class EavFactoryResultFunctionalTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->result = new EntityFactoryResult();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::getDomainKey
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::setDomainKey
     */
    public function domain_key() {
        $this->result->setDomainKey(1);
        $this->assertSame(1, $this->result->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::getSetKey
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::setSetKey
     */
    public function set_key() {
        $this->result->setSetKey(1);
        $this->assertSame(1, $this->result->getSetKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::getEntityKey
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::setEntityKey
     */
    public function entity_model() {
        $this->result->setEntityKey(1);
        $this->assertSame(1, $this->result->getEntityKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::addAttribute
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::getAttributes
     */
    public function attributes() {
        $this->assertEquals([], $this->result->getAttributes());
        $record = [_ATTR::NAME->column() => 'test'];
        $this->result->addAttribute($record);
        $this->assertEquals(['test' => $record], $this->result->getAttributes());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::addValue
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::getValues
     */
    public function values() {
        $this->assertEquals([], $this->result->getValues());
        $this->result->addValue(321, 123);
        $this->assertEquals([321 => 123], $this->result->getValues());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::addPivot
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::getPivots
     */
    public function pivots() {
        $this->assertEquals([], $this->result->getPivots());
        $this->result->addPivot(2,1);
        $this->assertEquals([2 => 1], $this->result->getPivots());
    }
}