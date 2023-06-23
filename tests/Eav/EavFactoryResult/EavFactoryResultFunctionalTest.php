<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EavFactoryResult;

use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueStringModel;
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
        $record = new AttributeModel();
        $this->result->addAttribute($record);
        $this->assertEquals([$record->getName() => $record], $this->result->getAttributes());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::addValue
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::getValues
     */
    public function values() {
        $this->assertEquals([], $this->result->getValues());
        $record = new ValueStringModel();
        $this->result->addValue("test", $record);
        $this->assertEquals(["test" => $record], $this->result->getValues());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::addPivot
     * @covers \Drobotik\Eav\Result\EntityFactoryResult::getPivots
     */
    public function pivots() {
        $this->assertEquals([], $this->result->getPivots());
        $record = new PivotModel();
        $this->result->addPivot("test", $record);
        $this->assertEquals(["test" => $record], $this->result->getPivots());
    }
}