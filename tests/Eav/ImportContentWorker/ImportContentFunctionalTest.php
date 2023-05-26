<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportContentWorker;

use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Content\AttributeSet;
use Drobotik\Eav\Import\Content\ValueSet;
use Drobotik\Eav\Import\Content\Worker;
use Drobotik\Eav\Model\AttributeModel;
use PHPUnit\Framework\TestCase;

class ImportContentFunctionalTest extends TestCase
{
    private Worker $worker;

    public function setUp(): void
    {
        parent::setUp();
        $this->worker = new Worker();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::__construct
     * @covers \Drobotik\Eav\Import\Content\Worker::getValueSet
     */
    public function value_set()
    {
        $this->assertInstanceOf(ValueSet::class, $this->worker->getValueSet());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::__construct
     * @covers \Drobotik\Eav\Import\Content\Worker::getAttributeSet
     */
    public function attribute_set()
    {
        $this->assertInstanceOf(AttributeSet::class, $this->worker->getAttributeSet());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::makeBulkValuesSet
     */
    public function make_bulk_values_set()
    {
        $set = $this->worker->makeBulkValuesSet();
        $this->assertInstanceOf(ValueSet::class, $this->worker->makeBulkValuesSet());
        $this->assertNotSame($set, $this->worker->getValueSet());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::parseCell
     */
    public function parse_cell()
    {
        $attributeKey = 123;
        $attributeName = 'test';
        $attributeType = ATTR_TYPE::TEXT;
        $content = 'text';

        $attributeModel = $this->getMockBuilder(AttributeModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getKey', 'getName', 'getTypeEnum'])->getMock();
        $attributeModel->method('getKey')->willReturn($attributeKey);
        $attributeModel->method('getName')->willReturn($attributeName);
        $attributeModel->method('getTypeEnum')->willReturn($attributeType);

        $attrSet = new AttributeSet();
        $attrSet->appendAttribute($attributeModel);

        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['getAttributeSet'])->getMock();
        $worker->method('getAttributeSet')->willReturn($attrSet);

        $worker->parseCell($attributeName, $content);

        $valueSet = $worker->getValueSet();
        $values = $valueSet->getValues();

        $this->assertCount(1, $values);
        $value = $values[0];

        $this->assertEquals($content, $value->getValue());
        $this->assertEquals($attributeType, $value->getType());
        $this->assertEquals($attributeName, $value->getAttributeName());
        $this->assertEquals($attributeKey, $value->getAttributeKey());
        $this->assertFalse($value->isEntityKey());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::parseCell
     */
    public function parse_cell_with_entity_key()
    {
        $entityKey = 451;
        $attributeKey = 123;
        $attributeName = 'test';
        $attributeType = ATTR_TYPE::TEXT;
        $content = 'text';

        $attributeModel = $this->getMockBuilder(AttributeModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getKey', 'getName', 'getTypeEnum'])->getMock();
        $attributeModel->method('getKey')->willReturn($attributeKey);
        $attributeModel->method('getName')->willReturn($attributeName);
        $attributeModel->method('getTypeEnum')->willReturn($attributeType);

        $attrSet = new AttributeSet();
        $attrSet->appendAttribute($attributeModel);

        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['getAttributeSet'])->getMock();
        $worker->method('getAttributeSet')->willReturn($attrSet);

        $worker->parseCell($attributeName, $content, $entityKey);
        $valueSet = $worker->getValueSet();
        $values = $valueSet->getValues();
        $value = $values[0];
        $this->assertEquals($entityKey, $value->getEntityKey());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::parseLine
     */
    public function parse_line_with_entity_key()
    {
        $line = [_ENTITY::ID->column() => 1,"name" => "Tom", "type" => "cat"];
        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['parseCell'])->getMock();
        $worker->expects($this->exactly(2))
            ->method('parseCell')
            ->withConsecutive(["name", "Tom", 1], ["type", "cat", 1]);
        $worker->parseLine($line);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::parseLine
     */
    public function parse_line_without_entity_key()
    {
        $line = ["name" => "Tom", "type" => "cat"];
        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['parseCell'])->getMock();
        $worker->expects($this->exactly(2))
            ->method('parseCell')
            ->withConsecutive(["name", "Tom", null], ["type", "cat", null]);
        $worker->parseLine($line);
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::parseChunk
     */
    public function parse_chunk()
    {
        $line1 = ["name" => "Tom", "type" => "cat"];
        $line2 = ["name" => "Jerry", "type" => "mouse"];
        $chunk = [$line1, $line2];
        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['parseLine'])->getMock();
        $worker->expects($this->exactly(2))
            ->method('parseLine')
            ->withConsecutive([$line1], [$line2]);
        $worker->parseChunk($chunk);
    }
}