<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportManager;

use Carbon\Carbon;
use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Attributes\Config;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Import\ImportManager;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueDatetimeModel;
use Drobotik\Eav\Model\ValueDecimalModel;
use Drobotik\Eav\Model\ValueIntegerModel;
use Drobotik\Eav\Model\ValueStringModel;
use Drobotik\Eav\Model\ValueTextModel;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use Tests\TestCase;

class ImportManagerAcceptanceTest extends TestCase
{
    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\Import\ImportManager::run
     */
    public function import_all_new()
    {
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain->getKey());
        $group = $this->eavFactory->createGroup($attrSet->getKey());

        $domainKey = $domain->getKey();
        $setKey = $attrSet->getKey();
        $groupKey = $group->getKey();

        $stringConfig  = new ConfigAttribute();
        $stringConfig->setFields([
            _ATTR::NAME->column() =>  ATTR_TYPE::STRING->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
        ]);
        $stringConfig->setGroupKey($groupKey);

        $integerConfig  = new ConfigAttribute();
        $integerConfig->setFields([
            _ATTR::NAME->column() => ATTR_TYPE::INTEGER->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
        ]);
        $integerConfig->setGroupKey($groupKey);

        $decimalConfig  = new ConfigAttribute();
        $decimalConfig->setFields([
            _ATTR::NAME->column() => ATTR_TYPE::DECIMAL->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
        ]);
        $decimalConfig->setGroupKey($groupKey);

        $datetimeConfig  = new ConfigAttribute();
        $datetimeConfig->setFields([
            _ATTR::NAME->column() => ATTR_TYPE::DATETIME->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
        ]);
        $datetimeConfig->setGroupKey($groupKey);

        $textConfig  = new ConfigAttribute();
        $textConfig->setFields([
            _ATTR::NAME->column() => ATTR_TYPE::TEXT->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
        ]);
        $textConfig->setGroupKey($groupKey);

        $attributesConfig = new Config();
        $attributesConfig->appendAttribute($stringConfig);
        $attributesConfig->appendAttribute($integerConfig);
        $attributesConfig->appendAttribute($decimalConfig);
        $attributesConfig->appendAttribute($datetimeConfig);
        $attributesConfig->appendAttribute($textConfig);

        $file = new \SplFileObject(dirname(__DIR__,2).'/Data/test.csv', 'r');
        $reader = Reader::createFromFileObject($file);
        $reader->setDelimiter(',');
        $reader->setHeaderOffset(0);

        $driver = new CsvDriver();
        $driver->setCursor(0);
        $driver->setChunkSize(50);
        $driver->setReader($reader);

        $contentWorker = new \Drobotik\Eav\Import\Content\Worker();

        $attributesWorker = new \Drobotik\Eav\Import\Attributes\Worker();
        $attributesWorker->setConfig($attributesConfig);

        $importManager = new ImportManager();

        $importContainer = new ImportContainer();
        $importContainer->setDomainKey($domainKey);
        $importContainer->setSetKey($setKey);
        $importContainer->setDriver($driver);
        $importContainer->setAttributesWorker($attributesWorker);
        $importContainer->setContentWorker($contentWorker);
        $importContainer->setManager($importManager);

        $importManager->run();

        // check attributes created
        /** @var AttributeModel $string */
        /** @var AttributeModel $integer */
        /** @var AttributeModel $decimal */
        /** @var AttributeModel $datetime */
        /** @var AttributeModel $text */
        $string = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::STRING->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::STRING->value())->first();
        $integer = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::INTEGER->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::INTEGER->value())->first();
        $decimal = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DECIMAL->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::DECIMAL->value())->first();
        $datetime = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DATETIME->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::DATETIME->value())->first();
        $text = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::TEXT->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::TEXT->value())->first();

        $this->assertNotNull($string);
        $this->assertNotNull($integer);
        $this->assertNotNull($decimal);
        $this->assertNotNull($datetime);
        $this->assertNotNull($text);

        // check attributes linked
        /** @var PivotModel $stringPivot */
        /** @var PivotModel $integerPivot */
        /** @var PivotModel $decimalPivot */
        /** @var PivotModel $datetimePivot */
        /** @var PivotModel $textPivot */
        $stringPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $string->getKey())->first();
        $integerPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $integer->getKey())->first();
        $decimalPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $decimal->getKey())->first();
        $datetimePivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $datetime->getKey())->first();
        $textPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $text->getKey())->first();

        $this->assertNotNull($stringPivot);
        $this->assertNotNull($integerPivot);
        $this->assertNotNull($decimalPivot);
        $this->assertNotNull($datetimePivot);
        $this->assertNotNull($textPivot);

        // check entities created
        $entities = EntityModel::query()->get();
        $this->assertEquals(100, $entities->count());


        // check values created
        $stmt = new Statement();
        $records = $stmt->process($reader);
        $outputSize = $records->count();

        $this->assertEquals(100, $outputSize);

        $iteration = 0;
        foreach ($records as $record)
        {
            /** @var EntityModel $entity */
            $entity = $entities[$iteration];

            /** @var ValueStringModel $stringValue */
            /** @var ValueIntegerModel $integerValue */
            /** @var ValueDecimalModel $decimalValue */
            /** @var ValueDatetimeModel $datetimeValue */
            /** @var ValueTextModel $textValue */
            $stringValue = ValueStringModel::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
                ->where(_VALUE::ENTITY_ID->column(), $entity->getKey())
                ->where(_VALUE::ATTRIBUTE_ID->column(), $string->getKey())
                ->first();
            $integerValue = ValueIntegerModel::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
                ->where(_VALUE::ENTITY_ID->column(), $entity->getKey())
                ->where(_VALUE::ATTRIBUTE_ID->column(), $integer->getKey())
                ->first();
            $decimalValue = ValueDecimalModel::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
                ->where(_VALUE::ENTITY_ID->column(), $entity->getKey())
                ->where(_VALUE::ATTRIBUTE_ID->column(), $decimal->getKey())
                ->first();
            $datetimeValue = ValueDatetimeModel::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
                ->where(_VALUE::ENTITY_ID->column(), $entity->getKey())
                ->where(_VALUE::ATTRIBUTE_ID->column(), $datetime->getKey())
                ->first();
            $textValue = ValueTextModel::where(_VALUE::DOMAIN_ID->column(), $domain->getKey())
                ->where(_VALUE::ENTITY_ID->column(), $entity->getKey())
                ->where(_VALUE::ATTRIBUTE_ID->column(), $text->getKey())
                ->first();

            $this->assertNotNull($stringValue);
            $this->assertNotNull($integerValue);
            $this->assertNotNull($decimalValue);
            $this->assertNotNull($datetimeValue);
            $this->assertNotNull($textValue);

            $this->assertEquals($record[$string->getName()], $stringValue->getValue(), "Iteration:$iteration");
            $this->assertEquals($record[$integer->getName()], $integerValue->getValue(), "Iteration:$iteration");
            $this->assertEquals($record[$decimal->getName()], $decimalValue->getValue(), "Iteration:$iteration");
            $this->assertEquals($record[$datetime->getName()], $datetimeValue->getValue(), "Iteration:$iteration");
            $this->assertEquals($record[$text->getName()], $textValue->getValue(), "Iteration:$iteration");

            $iteration++;
        }
    }

    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\Import\ImportManager::run
     */
    public function import_new_and_update()
    {
        $domain = $this->eavFactory->createDomain();
        $attrSet = $this->eavFactory->createAttributeSet($domain->getKey());
        $group = $this->eavFactory->createGroup($attrSet->getKey());

        $domainKey = $domain->getKey();
        $setKey = $attrSet->getKey();
        $groupKey = $group->getKey();

        $stringAttribute = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::DOMAIN_ID->column() => $domainKey,
            _ATTR::NAME->column() => ATTR_TYPE::STRING->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value(),
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $stringAttribute->getKey());
        $integerAttribute = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::DOMAIN_ID->column() => $domainKey,
            _ATTR::NAME->column() => ATTR_TYPE::INTEGER->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $integerAttribute->getKey());

        $oldValues = [];
        for($i=0; $i<6; $i++)
        {
            $entityModel = $this->eavFactory->createEntity($domainKey, $setKey);
            $entityKey = $entityModel->getKey();
            $stringValue = $this->eavFactory->createValue(
                ATTR_TYPE::STRING, $domainKey, $entityKey, $stringAttribute->getKey(), $this->faker->word);
            $integerValue = $this->eavFactory->createValue(
                ATTR_TYPE::STRING, $domainKey, $entityKey, $integerAttribute->getKey(), $this->faker->randomNumber());
            $oldValues[] = [$entityKey, $stringValue->getValue(), $integerValue->getValue()];
        }

        $this->assertEquals(6, EntityModel::query()->count());

        $newValues = $oldValues;
        $newValues[0] = [$oldValues[0][0], $this->faker->word, $this->faker->randomNumber(), $this->faker->randomFloat(), Carbon::now()->toISOString()];
        $newValues[1] = [$oldValues[1][0], $this->faker->word, $oldValues[1][2], $this->faker->randomFloat(), ''];
        $newValues[2] = [$oldValues[2][0], $oldValues[2][1], $this->faker->randomNumber(), '', Carbon::now()->toISOString()];
        $newValues[3] = [$oldValues[3][0], $oldValues[3][1], $oldValues[3][2], $this->faker->randomFloat(), Carbon::now()->toISOString()];
        $newValues[4] = [$oldValues[4][0], $this->faker->word, $oldValues[4][2], $this->faker->randomFloat(), Carbon::now()->toISOString()];
        $newValues[5] = [$oldValues[5][0], $oldValues[5][1], $this->faker->randomNumber(), $this->faker->randomFloat(), Carbon::now()->toISOString()];
        $newValues[] = ['', $this->faker->word, $this->faker->randomNumber(), $this->faker->randomFloat(), Carbon::now()->toISOString()];
        $newValues[] = ['', '', $this->faker->randomNumber(), $this->faker->randomFloat(), Carbon::now()->toISOString()];
        $newValues[] = ['', '', '', $this->faker->randomFloat(), Carbon::now()->toISOString()];
        $newValues[] = ['', '', '', '', Carbon::now()->toISOString()];
        $newValues[] = ['', '', '', '', ''];

        $file = new \SplFileObject(dirname(__DIR__, 2).'/temp/csv.csv', 'w+');
        $writer = Writer::createFromFileObject($file);
        $writer->insertOne([
            _ENTITY::ID->column(),
            ATTR_TYPE::STRING->value(),
            ATTR_TYPE::INTEGER->value(),
            ATTR_TYPE::DECIMAL->value(),
            ATTR_TYPE::DATETIME->value()
        ]);
        $writer->insertAll($newValues);

        $decimalConfig  = new ConfigAttribute();
        $decimalConfig->setFields([
            _ATTR::NAME->column() => ATTR_TYPE::DECIMAL->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
        ]);
        $decimalConfig->setGroupKey($groupKey);

        $datetimeConfig  = new ConfigAttribute();
        $datetimeConfig->setFields([
            _ATTR::NAME->column() => ATTR_TYPE::DATETIME->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
        ]);
        $datetimeConfig->setGroupKey($groupKey);

        $textConfig  = new ConfigAttribute();
        $textConfig->setFields([
            _ATTR::NAME->column() => ATTR_TYPE::TEXT->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
        ]);
        $textConfig->setGroupKey($groupKey);

        $attributesConfig = new Config();
        $attributesConfig->appendAttribute($decimalConfig);
        $attributesConfig->appendAttribute($datetimeConfig);
        $attributesConfig->appendAttribute($textConfig);

        $file = new \SplFileObject(dirname(__DIR__,2).'/Data/csv.csv', 'r');
        $reader = Reader::createFromFileObject($file);
        $reader->setDelimiter(',');
        $reader->setHeaderOffset(0);

        $driver = new CsvDriver();
        $driver->setCursor(0);
        $driver->setChunkSize(50);
        $driver->setReader($reader);

        $contentWorker = new \Drobotik\Eav\Import\Content\Worker();

        $attributesWorker = new \Drobotik\Eav\Import\Attributes\Worker();
        $attributesWorker->setConfig($attributesConfig);

        $importManager = new ImportManager();

        $importContainer = new ImportContainer();
        $importContainer->setDomainKey($domainKey);
        $importContainer->setSetKey($setKey);
        $importContainer->setDriver($driver);
        $importContainer->setAttributesWorker($attributesWorker);
        $importContainer->setContentWorker($contentWorker);
        $importContainer->setManager($importManager);

        $importManager->run();

        // check attributes created
        /** @var AttributeModel $string */
        /** @var AttributeModel $integer */
        /** @var AttributeModel $decimal */
        /** @var AttributeModel $datetime */
        /** @var AttributeModel $text */
        $string = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::STRING->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::STRING->value())->first();
        $integer = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::INTEGER->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::INTEGER->value())->first();
        $decimal = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DECIMAL->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::DECIMAL->value())->first();
        $datetime = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::DATETIME->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::DATETIME->value())->first();
        $text = AttributeModel::where(_ATTR::DOMAIN_ID->column(), $domain->getKey())
            ->where(_ATTR::TYPE->column(), ATTR_TYPE::TEXT->value())
            ->where(_ATTR::NAME->column(), ATTR_TYPE::TEXT->value())->first();

        $this->assertNotNull($string);
        $this->assertNotNull($integer);
        $this->assertNotNull($decimal);
        $this->assertNotNull($datetime);
        $this->assertNotNull($text);

        // check attributes linked
        /** @var PivotModel $stringPivot */
        /** @var PivotModel $integerPivot */
        /** @var PivotModel $decimalPivot */
        /** @var PivotModel $datetimePivot */
        /** @var PivotModel $textPivot */
        $stringPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $string->getKey())->first();
        $integerPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $integer->getKey())->first();
        $decimalPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $decimal->getKey())->first();
        $datetimePivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $datetime->getKey())->first();
        $textPivot = PivotModel::where(_PIVOT::DOMAIN_ID->column(), $domain->getKey())
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $text->getKey())->first();

        $this->assertNotNull($stringPivot);
        $this->assertNotNull($integerPivot);
        $this->assertNotNull($decimalPivot);
        $this->assertNotNull($datetimePivot);
        $this->assertNotNull($textPivot);

        // check entities created
        $entities = EntityModel::query()->get();
        $this->assertEquals(11, $entities->count());

        // check values created
        $stmt = new Statement();
        $records = $stmt->process($reader);
        $outputSize = $records->count();

        $this->assertEquals(11, $outputSize);

        $iteration = 0;
        foreach ($records as $record)
        {
            /** @var EntityModel $entity */
            $entity = $entities[$iteration];

            foreach($record as $attributeName => $value)
            {
                if($attributeName == _ENTITY::ID->column()) continue;
                $attribute = match ($attributeName) {
                    ATTR_TYPE::STRING->value() => $string,
                    ATTR_TYPE::INTEGER->value() => $integer,
                    ATTR_TYPE::DECIMAL->value() => $decimal,
                    ATTR_TYPE::DATETIME->value() => $datetime,
                };

                $valueRecord = $attribute->getTypeEnum()->model()
                    ->where(_VALUE::ENTITY_ID->column(), $entity->getKey())
                    ->where(_VALUE::ATTRIBUTE_ID->column(), $attribute->getKey())
                    ->first();

                if($value == '')
                {
                    $this->assertNull($valueRecord, "Unexpected value! Iteration:$iteration,Attribute:$attributeName");
                }
                else
                {
                    $this->assertNotNull($valueRecord);
                    $this->assertEquals($value, $valueRecord->getValue());
                }
            }
            $iteration++;
        }
    }
}