<?php

namespace Tests\Unit\EavFactoryResult;

use Kuperwood\Eav\Model\AttributeModel;
use Kuperwood\Eav\Model\EntityModel;
use Kuperwood\Eav\Model\PivotModel;
use Kuperwood\Eav\Model\ValueStringModel;
use PHPUnit\Framework\TestCase;
use Kuperwood\Eav\Result\EntityFactoryResult;

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
     * @covers EavFactoryResult::getEntityModel, EavFactoryResult::setEntityModel
     */
    public function entity_model() {
        $record = new EntityModel();
        $this->result->setEntityModel($record);
        $this->assertSame($record, $this->result->getEntityModel());
    }
    /**
     * @test
     * @group functional
     * @covers EavFactoryResult::addAttribute, EavFactoryResult::getAttributes
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
     * @covers EavFactoryResult::addValue, EavFactoryResult::getValues
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
     * @covers EavFactoryResult::addValue, EavFactoryResult::getValues
     */
    public function pivots() {
        $this->assertEquals([], $this->result->getPivots());
        $record = new PivotModel();
        $this->result->addPivot("test", $record);
        $this->assertEquals(["test" => $record], $this->result->getPivots());
    }
}