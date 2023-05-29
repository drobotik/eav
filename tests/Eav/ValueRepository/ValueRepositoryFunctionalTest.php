<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ValueRepository;

use Carbon\Carbon;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Content\Value;
use Drobotik\Eav\Import\Content\ValueSet;
use Drobotik\Eav\Model\ValueDatetimeModel;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueIntegerModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Model\ValueTextModel;
use Drobotik\Eav\Repository\ValueRepository;
use Tests\TestCase;

class ValueRepositoryFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Repository\ValueRepository::updateOrCreate
     */
    public function create_if_not_exist() {
        $repository = new ValueRepository();

        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;
        $type = ATTR_TYPE::INTEGER;
        $value = 23;

        $record = $repository->updateOrCreate($domainKey,$entityKey, $attributeKey, $type, $value);
        $this->assertInstanceOf(ValueIntegerModel::class, $record);
        $this->assertEquals($domainKey, $record->getDomainKey());
        $this->assertEquals($entityKey, $record->getEntityKey());
        $this->assertEquals($attributeKey, $record->getAttrKey());
        $this->assertEquals($value, $record->getValue());

        $value = 35;
        $record = $repository->updateOrCreate($domainKey, $entityKey, $attributeKey, $type, $value);
        $this->assertEquals($value, $record->getValue());

        $this->assertEquals(1, ValueIntegerModel::query()->count());

        $entityKey = 3;
        $repository->updateOrCreate($domainKey, $entityKey, $attributeKey, $type, $value);

        $this->assertEquals(2, ValueIntegerModel::query()->count());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Repository\ValueRepository::bulkCreate
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

        $repository = new ValueRepository();
        $repository->bulkCreate($set, $domainKey);

        $stringRecords = ValueStringModel::query()->get();
        $this->assertEquals(2, $stringRecords->count());
        /** @var ValueStringModel[] $stringRecords */
        $this->assertEquals($domainKey, $stringRecords[0]->getDomainKey());
        $this->assertEquals($valueString1->getAttributeKey(), $stringRecords[0]->getAttrKey());
        $this->assertEquals($valueString1->getEntityKey(), $stringRecords[0]->getEntityKey());
        $this->assertEquals($valueString1->getValue(), $stringRecords[0]->getValue());
        $this->assertEquals($domainKey, $stringRecords[1]->getDomainKey());
        $this->assertEquals($valueString2->getAttributeKey(), $stringRecords[1]->getAttrKey());
        $this->assertEquals($valueString2->getEntityKey(), $stringRecords[1]->getEntityKey());
        $this->assertEquals($valueString2->getValue(), $stringRecords[1]->getValue());

        $integerRecords = ValueIntegerModel::query()->get();
        $this->assertEquals(2, $integerRecords->count());
        /** @var ValueIntegerModel[] $integerRecords */
        $this->assertEquals($domainKey, $integerRecords[0]->getDomainKey());
        $this->assertEquals($valueInteger1->getAttributeKey(), $integerRecords[0]->getAttrKey());
        $this->assertEquals($valueInteger1->getEntityKey(), $integerRecords[0]->getEntityKey());
        $this->assertEquals($valueInteger1->getValue(), $integerRecords[0]->getValue());
        $this->assertEquals($domainKey, $integerRecords[1]->getDomainKey());
        $this->assertEquals($valueInteger2->getAttributeKey(), $integerRecords[1]->getAttrKey());
        $this->assertEquals($valueInteger2->getEntityKey(), $integerRecords[1]->getEntityKey());
        $this->assertEquals($valueInteger2->getValue(), $integerRecords[1]->getValue());

        $decimalRecords = ValueDecimalModel::query()->get();
        $this->assertEquals(2, $decimalRecords->count());
        /** @var ValueDecimalModel[] $decimalRecords */
        $this->assertEquals($domainKey, $decimalRecords[0]->getDomainKey());
        $this->assertEquals($valueDecimal1->getAttributeKey(), $decimalRecords[0]->getAttrKey());
        $this->assertEquals($valueDecimal1->getEntityKey(), $decimalRecords[0]->getEntityKey());
        $this->assertEquals($valueDecimal1->getValue(), $decimalRecords[0]->getValue());
        $this->assertEquals($domainKey, $decimalRecords[1]->getDomainKey());
        $this->assertEquals($valueDecimal2->getAttributeKey(), $decimalRecords[1]->getAttrKey());
        $this->assertEquals($valueDecimal2->getEntityKey(), $decimalRecords[1]->getEntityKey());
        $this->assertEquals($valueDecimal2->getValue(), $decimalRecords[1]->getValue());

        $datetimeRecords = ValueDatetimeModel::query()->get();
        $this->assertEquals(2, $datetimeRecords->count());
        /** @var ValueDatetimeModel[] $datetimeRecords */
        $this->assertEquals($domainKey, $datetimeRecords[0]->getDomainKey());
        $this->assertEquals($valueDatetime1->getAttributeKey(), $datetimeRecords[0]->getAttrKey());
        $this->assertEquals($valueDatetime1->getEntityKey(), $datetimeRecords[0]->getEntityKey());
        $this->assertEquals($valueDatetime1->getValue(), $datetimeRecords[0]->getValue());
        $this->assertEquals($domainKey, $datetimeRecords[1]->getDomainKey());
        $this->assertEquals($valueDatetime2->getAttributeKey(), $datetimeRecords[1]->getAttrKey());
        $this->assertEquals($valueDatetime2->getEntityKey(), $datetimeRecords[1]->getEntityKey());
        $this->assertEquals($valueDatetime2->getValue(), $datetimeRecords[1]->getValue());

        $textRecords = ValueTextModel::query()->get();
        $this->assertEquals(2, $textRecords->count());
        /** @var ValueTextModel[] $textRecords */
        $this->assertEquals($domainKey, $textRecords[0]->getDomainKey());
        $this->assertEquals($valueText1->getAttributeKey(), $textRecords[0]->getAttrKey());
        $this->assertEquals($valueText1->getEntityKey(), $textRecords[0]->getEntityKey());
        $this->assertEquals($valueText1->getValue(), $textRecords[0]->getValue());
        $this->assertEquals($domainKey, $textRecords[1]->getDomainKey());
        $this->assertEquals($valueText2->getAttributeKey(), $textRecords[1]->getAttrKey());
        $this->assertEquals($valueText2->getEntityKey(), $textRecords[1]->getEntityKey());
        $this->assertEquals($valueText2->getValue(), $textRecords[1]->getValue());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Repository\ValueRepository::destroy
     */
    public function destroy()
    {
        $domainKey = 1;
        $entityKey = 2;
        $attributeKey = 3;

        $this->eavFactory->createValue(ATTR_TYPE::STRING, $domainKey, $entityKey, $attributeKey, 'test');

        $repository = new ValueRepository();
        $repository->destroy($domainKey, $entityKey, $attributeKey, ATTR_TYPE::STRING);
        $this->assertFalse(ValueStringModel::query()
            ->where(_VALUE::DOMAIN_ID->column(), $domainKey)
            ->where(_VALUE::ENTITY_ID->column(), $entityKey)
            ->where(_VALUE::ATTRIBUTE_ID->column(), $attributeKey)
        ->exists());
    }


}