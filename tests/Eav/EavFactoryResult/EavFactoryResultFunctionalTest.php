<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EavFactoryResult;

use Kuperwood\Eav\Enum\_ATTR;
use PHPUnit\Framework\TestCase;
use Kuperwood\Eav\Result\EntityFactoryResult;

class EavFactoryResultFunctionalTest extends TestCase
{
    private EntityFactoryResult $result;
    public function setUp(): void
    {
        parent::setUp();
        $this->result = new EntityFactoryResult();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::getDomainKey
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::setDomainKey
     */
    public function domain_key() {
        $this->result->setDomainKey(1);
        $this->assertSame(1, $this->result->getDomainKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::getSetKey
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::setSetKey
     */
    public function set_key() {
        $this->result->setSetKey(1);
        $this->assertSame(1, $this->result->getSetKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::getEntityKey
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::setEntityKey
     */
    public function entity_model() {
        $this->result->setEntityKey(1);
        $this->assertSame(1, $this->result->getEntityKey());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::addAttribute
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::getAttributes
     */
    public function attributes() {
        $this->assertEquals([], $this->result->getAttributes());
        $record = [_ATTR::NAME => 'test'];
        $this->result->addAttribute($record);
        $this->assertEquals(['test' => $record], $this->result->getAttributes());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::addValue
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::getValues
     */
    public function values() {
        $this->assertEquals([], $this->result->getValues());
        $this->result->addValue(321, 123);
        $this->assertEquals([321 => 123], $this->result->getValues());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::addPivot
     * @covers \Kuperwood\Eav\Result\EntityFactoryResult::getPivots
     */
    public function pivots() {
        $this->assertEquals([], $this->result->getPivots());
        $this->result->addPivot(2,1);
        $this->assertEquals([2 => 1], $this->result->getPivots());
    }
}