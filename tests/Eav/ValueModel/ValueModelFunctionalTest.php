<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueModel;

use Carbon\Carbon;
use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Content\Value;
use Drobotik\Eav\Import\Content\ValueSet;
use Drobotik\Eav\Model\ValueBase;
use Drobotik\Eav\Value\ValueParser;
use Tests\TestCase;

class ValueModelFunctionalTest extends TestCase
{
    private ValueBase $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->model = new ValueBase();
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\ValueBase::__construct
     */
    public function defaults() {
        $this->assertEquals(_VALUE::ID, $this->model->getPrimaryKey());
    }

    /**
     * @return ATTR_TYPE[]
     */
    private function cases(): array
    {
        return [ATTR_TYPE::STRING, ATTR_TYPE::INTEGER, ATTR_TYPE::DECIMAL, ATTR_TYPE::DATETIME, ATTR_TYPE::TEXT];
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\ValueBase::create
     */
    public function create_record()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;
        $parser = new ValueParser();
        foreach($this->cases() as $case)
        {
            $value = $case->randomValue();
            $valueKey = $this->model->create($case->valueTable(), $domainKey, $entityKey, $attributeKey, $value);
            $valueRecord = Connection::get()->createQueryBuilder()
                ->select('*')
                ->from($case->valueTable())
                ->where(sprintf('%s = ? AND %s = ? AND %s = ?',
                    _VALUE::DOMAIN_ID, _VALUE::ENTITY_ID, _VALUE::ATTRIBUTE_ID
                ))
                ->setParameters([$domainKey,$entityKey,$attributeKey])
                ->executeQuery()
                ->fetchAssociative();
            $this->assertEquals($valueKey, $valueRecord[_VALUE::ID], "Iteration:".$case->value());
            $this->assertEquals($parser->parse($case, $value), $valueRecord[_VALUE::VALUE], "Iteration:".$case->value());
        }
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\ValueBase::find
     */
    public function find_record()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;

        foreach($this->cases() as $case)
        {
            $valueKey = $this->model->create($case->valueTable(), $domainKey, $entityKey, $attributeKey, $case->randomValue());
            $record = $this->model->find($case->valueTable(), $domainKey, $entityKey, $attributeKey);
            $this->assertEquals($valueKey, $record[_VALUE::ID]);
        }
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\ValueBase::update
     */
    public function update_record()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;
        foreach($this->cases() as $case)
        {
            $oldValue = $case->randomValue();
            $newValue = $case->randomValue();
            $this->model->create($case->valueTable(), $domainKey, $entityKey, $attributeKey, $oldValue);
            $result = $this->model->update($case->valueTable(), $domainKey, $entityKey, $attributeKey, $newValue);
            $this->assertEquals(1, $result);
            $record = $this->model->find($case->valueTable(), $domainKey, $entityKey, $attributeKey);
            $this->assertIsArray($record);
            $this->assertEquals($newValue, $record[_VALUE::VALUE]);
        }
    }

    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\Model\ValueBase::destroy
     */
    public function destroy_record()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;

        foreach($this->cases() as $case)
        {
            $this->model->create($case->valueTable(), $domainKey, $entityKey, $attributeKey, $case->randomValue());
            $result = $this->model->destroy($case->valueTable(), $domainKey, $entityKey, $attributeKey);
            $this->assertEquals(1, $result);
            $test = $this->model->find($case->valueTable(), $domainKey, $entityKey, $attributeKey);
            $this->assertFalse($test);
        }
    }


    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Model\ValueBase::bulkCreate
     */
    public function bulkCreate()
    {
        $valueString1 = new Value();
        $valueString1->setAttributeKey(1);
        $valueString1->setAttributeName('string1name');
        $valueString1->setEntityKey(1);
        $valueString1->setType(ATTR_TYPE::STRING);
        $valueString1->setValue('string1value');

        $valueInteger1 = new Value();
        $valueInteger1->setAttributeKey(2);
        $valueInteger1->setAttributeName('integer1name');
        $valueInteger1->setEntityKey(1);
        $valueInteger1->setType(ATTR_TYPE::INTEGER);
        $valueInteger1->setValue(123);

        $valueDecimal1 = new Value();
        $valueDecimal1->setAttributeKey(3);
        $valueDecimal1->setAttributeName('decimal1name');
        $valueDecimal1->setEntityKey(1);
        $valueDecimal1->setType(ATTR_TYPE::DECIMAL);
        $valueDecimal1->setValue(123.23);

        $valueDatetime1 = new Value();
        $valueDatetime1->setAttributeKey(4);
        $valueDatetime1->setAttributeName('datetime1name');
        $valueDatetime1->setEntityKey(1);
        $valueDatetime1->setType(ATTR_TYPE::DATETIME);
        $valueDatetime1->setValue(Carbon::now()->toISOString());

        $valueText1 = new Value();
        $valueText1->setAttributeKey(5);
        $valueText1->setAttributeName('text1name');
        $valueText1->setEntityKey(1);
        $valueText1->setType(ATTR_TYPE::TEXT);
        $valueText1->setValue('text 1');

        $valueString2 = new Value();
        $valueString2->setAttributeKey(1);
        $valueString2->setAttributeName('string2name');
        $valueString2->setEntityKey(2);
        $valueString2->setType(ATTR_TYPE::STRING);
        $valueString2->setValue('string1value');

        $valueInteger2 = new Value();
        $valueInteger2->setAttributeKey(2);
        $valueInteger2->setAttributeName('integer2name');
        $valueInteger2->setEntityKey(2);
        $valueInteger2->setType(ATTR_TYPE::INTEGER);
        $valueInteger2->setValue(123);

        $valueDecimal2 = new Value();
        $valueDecimal2->setAttributeKey(3);
        $valueDecimal2->setAttributeName('decimal2name');
        $valueDecimal2->setEntityKey(2);
        $valueDecimal2->setType(ATTR_TYPE::DECIMAL);
        $valueDecimal2->setValue(123.23);

        $valueDatetime2 = new Value();
        $valueDatetime2->setAttributeKey(4);
        $valueDatetime2->setAttributeName('datetime2name');
        $valueDatetime2->setEntityKey(2);
        $valueDatetime2->setType(ATTR_TYPE::DATETIME);
        $valueDatetime2->setValue(Carbon::now()->toISOString());

        $valueText2 = new Value();
        $valueText2->setAttributeKey(5);
        $valueText2->setAttributeName('text2name');
        $valueText2->setEntityKey(2);
        $valueText2->setType(ATTR_TYPE::TEXT);
        $valueText2->setValue('text 2');

        $set = new ValueSet();
        $set->appendValue($valueString1);
        $set->appendValue($valueInteger1);
        $set->appendValue($valueDecimal1);
        $set->appendValue($valueDatetime1);
        $set->appendValue($valueText1);

        $set->appendValue($valueString2);
        $set->appendValue($valueInteger2);
        $set->appendValue($valueDecimal2);
        $set->appendValue($valueDatetime2);
        $set->appendValue($valueText2);

        $domainKey = 1;

        $repository = new ValueBase();
        $repository->bulkCreate($set, $domainKey);

        $stringRecords = Connection::get()->createQueryBuilder()->select('*')->from(ATTR_TYPE::STRING->valueTable())
            ->executeQuery()->fetchAllAssociative();
        $this->assertEquals(2, count($stringRecords));
        $this->assertEquals($domainKey, $stringRecords[0][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueString1->getAttributeKey(), $stringRecords[0][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueString1->getEntityKey(), $stringRecords[0][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueString1->getValue(), $stringRecords[0][_VALUE::VALUE]);
        $this->assertEquals($domainKey, $stringRecords[1][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueString2->getAttributeKey(), $stringRecords[1][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueString2->getEntityKey(), $stringRecords[1][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueString2->getValue(), $stringRecords[1][_VALUE::VALUE]);

        $integerRecords = Connection::get()->createQueryBuilder()->select('*')->from(ATTR_TYPE::INTEGER->valueTable())
            ->executeQuery()->fetchAllAssociative();

        $this->assertEquals(2, count($integerRecords));
        $this->assertEquals($domainKey, $integerRecords[0][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueInteger1->getAttributeKey(), $integerRecords[0][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueInteger1->getEntityKey(), $integerRecords[0][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueInteger1->getValue(), $integerRecords[0][_VALUE::VALUE]);
        $this->assertEquals($domainKey, $integerRecords[1][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueInteger2->getAttributeKey(), $integerRecords[1][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueInteger2->getEntityKey(), $integerRecords[1][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueInteger2->getValue(), $integerRecords[1][_VALUE::VALUE]);

        $decimalRecords = Connection::get()->createQueryBuilder()->select('*')->from(ATTR_TYPE::DECIMAL->valueTable())
            ->executeQuery()->fetchAllAssociative();
        $this->assertEquals(2, count($decimalRecords));
        $this->assertEquals($domainKey, $decimalRecords[0][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueDecimal1->getAttributeKey(), $decimalRecords[0][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueDecimal1->getEntityKey(), $decimalRecords[0][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueDecimal1->getValue(), $decimalRecords[0][_VALUE::VALUE]);
        $this->assertEquals($domainKey, $decimalRecords[1][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueDecimal2->getAttributeKey(), $decimalRecords[1][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueDecimal2->getEntityKey(), $decimalRecords[1][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueDecimal2->getValue(), $decimalRecords[1][_VALUE::VALUE]);

        $datetimeRecords =  Connection::get()->createQueryBuilder()->select('*')->from(ATTR_TYPE::DATETIME->valueTable())
            ->executeQuery()->fetchAllAssociative();
        $this->assertEquals(2, count($datetimeRecords));
        $this->assertEquals($domainKey, $datetimeRecords[0][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueDatetime1->getAttributeKey(), $datetimeRecords[0][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueDatetime1->getEntityKey(), $datetimeRecords[0][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueDatetime1->getValue(), $datetimeRecords[0][_VALUE::VALUE]);
        $this->assertEquals($domainKey, $datetimeRecords[1][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueDatetime2->getAttributeKey(), $datetimeRecords[1][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueDatetime2->getEntityKey(), $datetimeRecords[1][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueDatetime2->getValue(), $datetimeRecords[1][_VALUE::VALUE]);

        $textRecords =  Connection::get()->createQueryBuilder()->select('*')->from(ATTR_TYPE::TEXT->valueTable())
            ->executeQuery()->fetchAllAssociative();
        $this->assertEquals(2, count($textRecords));
        $this->assertEquals($domainKey, $textRecords[0][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueText1->getAttributeKey(), $textRecords[0][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueText1->getEntityKey(), $textRecords[0][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueText1->getValue(), $textRecords[0][_VALUE::VALUE]);
        $this->assertEquals($domainKey, $textRecords[1][_VALUE::DOMAIN_ID]);
        $this->assertEquals($valueText2->getAttributeKey(), $textRecords[1][_VALUE::ATTRIBUTE_ID]);
        $this->assertEquals($valueText2->getEntityKey(), $textRecords[1][_VALUE::ENTITY_ID]);
        $this->assertEquals($valueText2->getValue(), $textRecords[1][_VALUE::VALUE]);
    }
}