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
use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Attributes\Config;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Import\ImportManager;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Model\ValueBase;
use Drobotik\Eav\Value\ValueParser;
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
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

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
        $qb = Connection::get()->createQueryBuilder();
        $q = $qb->select('*')->from(_ATTR::table())
            ->where(sprintf('%s = :domain AND %s = :type AND %s = :name',
                _ATTR::DOMAIN_ID->column(), _ATTR::TYPE->column(), _ATTR::NAME->column()));

        $string = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::STRING->value(),
            'name' => ATTR_TYPE::STRING->value()
        ])->executeQuery()->fetchAssociative();

        $integer = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::INTEGER->value(),
            'name' => ATTR_TYPE::INTEGER->value()
        ])->executeQuery()->fetchAssociative();

        $decimal =  $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DECIMAL->value(),
            'name' => ATTR_TYPE::DECIMAL->value()
        ])->executeQuery()->fetchAssociative();

        $datetime = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DATETIME->value(),
            'name' => ATTR_TYPE::DATETIME->value()
        ])->executeQuery()->fetchAssociative();

        $text = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::TEXT->value(),
            'name' => ATTR_TYPE::TEXT->value()
        ])->executeQuery()->fetchAssociative();

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        // check attributes linked
        $pivotModel = new PivotModel();
        $stringPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $string[_ATTR::ID->column()]);
        $integerPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $integer[_ATTR::ID->column()]);
        $decimalPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $decimal[_ATTR::ID->column()]);
        $datetimePivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $datetime[_ATTR::ID->column()]);
        $textPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $text[_ATTR::ID->column()]);

        $this->assertIsArray($stringPivot);
        $this->assertIsArray($integerPivot);
        $this->assertIsArray($decimalPivot);
        $this->assertIsArray($datetimePivot);
        $this->assertIsArray($textPivot);

        // check entities created

        $qb = Connection::get()->createQueryBuilder();

        $entities = $qb->select('*')->from(_ENTITY::table())
            ->executeQuery()->fetchAllAssociative();
        $this->assertEquals(100, count($entities));

        // check values created
        $stmt = new Statement();
        $records = $stmt->process($reader);
        $outputSize = $records->count();

        $this->assertEquals(100, $outputSize);

        $valueParser = new ValueParser();
        $valueModel = $this->makeValueModel();

        $iteration = 0;
        foreach ($records as $record)
        {
            /** @var EntityModel $entity */
            $entity = $entities[$iteration];
            $entityKey = $entity[_ENTITY::ID->column()];

            // check values created
            $stringValue = $valueModel->find(
                ATTR_TYPE::STRING->valueTable(),
                $domainKey,
                $entityKey,
                $string[_ATTR::ID->column()]
            );
            $integerValue = $valueModel->find(
                ATTR_TYPE::INTEGER->valueTable(),
                $domainKey,
                $entityKey,
                $integer[_ATTR::ID->column()]
            );
            $decimalValue = $valueModel->find(
                ATTR_TYPE::DECIMAL->valueTable(),
                $domainKey,
                $entityKey,
                $decimal[_ATTR::ID->column()]
            );
            $datetimeValue = $valueModel->find(
                ATTR_TYPE::DATETIME->valueTable(),
                $domainKey,
                $entityKey,
                $datetime[_ATTR::ID->column()]
            );
            $textValue = $valueModel->find(
                ATTR_TYPE::TEXT->valueTable(),
                $domainKey,
                $entityKey,
                $text[_ATTR::ID->column()]
            );

            $this->assertIsArray($stringValue);
            $this->assertIsArray($integerValue);
            $this->assertIsArray($decimalValue);
            $this->assertIsArray($datetimeValue);
            $this->assertIsArray($textValue);

            $this->assertEquals($valueParser->parse(ATTR_TYPE::STRING, $record[$string[_ATTR::NAME->column()]]), $stringValue[_VALUE::VALUE->column()], "Iteration:$iteration");
            $this->assertEquals($valueParser->parse(ATTR_TYPE::INTEGER, $record[$integer[_ATTR::NAME->column()]]), $integerValue[_VALUE::VALUE->column()], "Iteration:$iteration");
            $this->assertEquals($valueParser->parse(ATTR_TYPE::DECIMAL, $record[$decimal[_ATTR::NAME->column()]]), $decimalValue[_VALUE::VALUE->column()], "Iteration:$iteration");
            $this->assertEquals($valueParser->parse(ATTR_TYPE::DATETIME, $record[$datetime[_ATTR::NAME->column()]]), $datetimeValue[_VALUE::VALUE->column()], "Iteration:$iteration");
            $this->assertEquals($valueParser->parse(ATTR_TYPE::TEXT, $record[$text[_ATTR::NAME->column()]]), $textValue[_VALUE::VALUE->column()], "Iteration:$iteration");

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
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupKey = $this->eavFactory->createGroup($setKey);

        $stringAttributeKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::DOMAIN_ID->column() => $domainKey,
            _ATTR::NAME->column() => ATTR_TYPE::STRING->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::STRING->value(),
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey,$stringAttributeKey);
        $integerAttributeKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::DOMAIN_ID->column() => $domainKey,
            _ATTR::NAME->column() => ATTR_TYPE::INTEGER->value(),
            _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $integerAttributeKey);

        $oldValues = [];

        $valueModel = new ValueBase();
        for($i=0; $i<6; $i++)
        {
            $entityKey = $this->eavFactory->createEntity($domainKey, $setKey);
            $stringValueKey = $valueModel->create(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $stringAttributeKey, ATTR_TYPE::STRING->randomValue());
            $integerValueKey = $valueModel->create(ATTR_TYPE::STRING->valueTable(), $domainKey, $entityKey, $integerAttributeKey, ATTR_TYPE::INTEGER->randomValue());
            $oldValues[] = [$entityKey, $stringValueKey, $integerValueKey];
        }

        $model = new EntityModel();
        $this->assertEquals(6, $model->count());

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

        $file = new \SplFileObject(dirname(__DIR__, 2).'/Data/csv.csv', 'w');
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
        $qb = Connection::get()->createQueryBuilder();
        $q = $qb->select('*')->from(_ATTR::table())
            ->where(sprintf('%s = :domain AND %s = :type AND %s = :name',
                _ATTR::DOMAIN_ID->column(), _ATTR::TYPE->column(), _ATTR::NAME->column()));

        $string = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::STRING->value(),
            'name' => ATTR_TYPE::STRING->value()
        ])->executeQuery()->fetchAssociative();

        $integer = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::INTEGER->value(),
            'name' => ATTR_TYPE::INTEGER->value()
        ])->executeQuery()->fetchAssociative();

        $decimal =  $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DECIMAL->value(),
            'name' => ATTR_TYPE::DECIMAL->value()
        ])->executeQuery()->fetchAssociative();

        $datetime = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DATETIME->value(),
            'name' => ATTR_TYPE::DATETIME->value()
        ])->executeQuery()->fetchAssociative();

        $text = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::TEXT->value(),
            'name' => ATTR_TYPE::TEXT->value()
        ])->executeQuery()->fetchAssociative();

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        // check attributes linked
        $pivotModel = new PivotModel();
        $stringPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $string[_ATTR::ID->column()]);
        $integerPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $integer[_ATTR::ID->column()]);
        $decimalPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $decimal[_ATTR::ID->column()]);
        $datetimePivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $datetime[_ATTR::ID->column()]);
        $textPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $text[_ATTR::ID->column()]);

        $this->assertIsArray($stringPivot);
        $this->assertIsArray($integerPivot);
        $this->assertIsArray($decimalPivot);
        $this->assertIsArray($datetimePivot);
        $this->assertIsArray($textPivot);

        // check entities created
        $qb = Connection::get()->createQueryBuilder();
        $entities = $qb->select('*')->from(_ENTITY::table())
            ->executeQuery()->fetchAllAssociative();
        $this->assertEquals(11, count($entities));

        // check values created
        $stmt = new Statement();
        $records = $stmt->process($reader);
        $outputSize = $records->count();

        $this->assertEquals(11, $outputSize);
        $parser = $this->makeValueParser();

        $iteration = 0;
        foreach ($records as $record)
        {
            /** @var EntityModel $entity */
            $entity = $entities[$iteration];
            $entityKey = $entity[_ENTITY::ID->column()];

            foreach($record as $attributeName => $value)
            {
                if($attributeName == _ENTITY::ID->column()) continue;
                $attribute = match ($attributeName) {
                    ATTR_TYPE::STRING->value() => $string,
                    ATTR_TYPE::INTEGER->value() => $integer,
                    ATTR_TYPE::DECIMAL->value() => $decimal,
                    ATTR_TYPE::DATETIME->value() => $datetime,
                };

                $attrType = ATTR_TYPE::getCase($attribute[_ATTR::TYPE->column()]);
                $valueTable = $attrType->valueTable();
                $valueRecord = $valueModel->find($valueTable, $domainKey, $entityKey, $attribute[_ATTR::ID->column()]);

                if($value == '')
                {
                    $this->assertFalse($valueRecord, "Unexpected value! Iteration:$iteration,Attribute:$attributeName");
                }
                else
                {
                    $this->assertIsArray($valueRecord);
                    $this->assertEquals($parser->parse($attrType, $value), $valueRecord[_VALUE::VALUE->column()]);
                }
            }
            $iteration++;
        }
    }
}