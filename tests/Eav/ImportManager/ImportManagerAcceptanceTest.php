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
            _ATTR::NAME =>  ATTR_TYPE::STRING,
            _ATTR::TYPE => ATTR_TYPE::STRING
        ]);
        $stringConfig->setGroupKey($groupKey);

        $integerConfig  = new ConfigAttribute();
        $integerConfig->setFields([
            _ATTR::NAME => ATTR_TYPE::INTEGER,
            _ATTR::TYPE => ATTR_TYPE::INTEGER
        ]);
        $integerConfig->setGroupKey($groupKey);

        $decimalConfig  = new ConfigAttribute();
        $decimalConfig->setFields([
            _ATTR::NAME => ATTR_TYPE::DECIMAL,
            _ATTR::TYPE => ATTR_TYPE::DECIMAL
        ]);
        $decimalConfig->setGroupKey($groupKey);

        $datetimeConfig  = new ConfigAttribute();
        $datetimeConfig->setFields([
            _ATTR::NAME => ATTR_TYPE::DATETIME,
            _ATTR::TYPE => ATTR_TYPE::DATETIME
        ]);
        $datetimeConfig->setGroupKey($groupKey);

        $textConfig  = new ConfigAttribute();
        $textConfig->setFields([
            _ATTR::NAME => ATTR_TYPE::TEXT,
            _ATTR::TYPE => ATTR_TYPE::TEXT
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
                _ATTR::DOMAIN_ID, _ATTR::TYPE, _ATTR::NAME));

        $string = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::STRING,
            'name' => ATTR_TYPE::STRING
        ])->executeQuery()->fetchAssociative();

        $integer = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::INTEGER,
            'name' => ATTR_TYPE::INTEGER
        ])->executeQuery()->fetchAssociative();

        $decimal =  $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DECIMAL,
            'name' => ATTR_TYPE::DECIMAL
        ])->executeQuery()->fetchAssociative();

        $datetime = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DATETIME,
            'name' => ATTR_TYPE::DATETIME
        ])->executeQuery()->fetchAssociative();

        $text = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::TEXT,
            'name' => ATTR_TYPE::TEXT
        ])->executeQuery()->fetchAssociative();

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        // check attributes linked
        $pivotModel = new PivotModel();
        $stringPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $string[_ATTR::ID]);
        $integerPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $integer[_ATTR::ID]);
        $decimalPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $decimal[_ATTR::ID]);
        $datetimePivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $datetime[_ATTR::ID]);
        $textPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $text[_ATTR::ID]);

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
            $entityKey = $entity[_ENTITY::ID];

            // check values created
            $stringValue = $valueModel->find(
                ATTR_TYPE::valueTable(ATTR_TYPE::STRING),
                $domainKey,
                $entityKey,
                $string[_ATTR::ID]
            );
            $integerValue = $valueModel->find(
                ATTR_TYPE::valueTable(ATTR_TYPE::INTEGER),
                $domainKey,
                $entityKey,
                $integer[_ATTR::ID]
            );
            $decimalValue = $valueModel->find(
                ATTR_TYPE::valueTable(ATTR_TYPE::DECIMAL),
                $domainKey,
                $entityKey,
                $decimal[_ATTR::ID]
            );
            $datetimeValue = $valueModel->find(
                ATTR_TYPE::valueTable(ATTR_TYPE::DATETIME),
                $domainKey,
                $entityKey,
                $datetime[_ATTR::ID]
            );
            $textValue = $valueModel->find(
                ATTR_TYPE::valueTable(ATTR_TYPE::TEXT),
                $domainKey,
                $entityKey,
                $text[_ATTR::ID]
            );

            $this->assertIsArray($stringValue);
            $this->assertIsArray($integerValue);
            $this->assertIsArray($decimalValue);
            $this->assertIsArray($datetimeValue);
            $this->assertIsArray($textValue);

            $this->assertEquals($valueParser->parse(ATTR_TYPE::STRING, $record[$string[_ATTR::NAME]]), $stringValue[_VALUE::VALUE], "Iteration:$iteration");
            $this->assertEquals($valueParser->parse(ATTR_TYPE::INTEGER, $record[$integer[_ATTR::NAME]]), $integerValue[_VALUE::VALUE], "Iteration:$iteration");
            $this->assertEquals($valueParser->parse(ATTR_TYPE::DECIMAL, $record[$decimal[_ATTR::NAME]]), $decimalValue[_VALUE::VALUE], "Iteration:$iteration");
            $this->assertEquals($valueParser->parse(ATTR_TYPE::DATETIME, $record[$datetime[_ATTR::NAME]]), $datetimeValue[_VALUE::VALUE], "Iteration:$iteration");
            $this->assertEquals($valueParser->parse(ATTR_TYPE::TEXT, $record[$text[_ATTR::NAME]]), $textValue[_VALUE::VALUE], "Iteration:$iteration");

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
            _ATTR::DOMAIN_ID => $domainKey,
            _ATTR::NAME => ATTR_TYPE::STRING,
            _ATTR::TYPE => ATTR_TYPE::STRING,
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey,$stringAttributeKey);
        $integerAttributeKey = $this->eavFactory->createAttribute($domainKey, [
            _ATTR::DOMAIN_ID => $domainKey,
            _ATTR::NAME => ATTR_TYPE::INTEGER,
            _ATTR::TYPE => ATTR_TYPE::INTEGER,
        ]);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupKey, $integerAttributeKey);

        $oldValues = [];

        $valueModel = new ValueBase();
        for($i=0; $i<6; $i++)
        {
            $entityKey = $this->eavFactory->createEntity($domainKey, $setKey);
            $stringValueKey = $valueModel->create(ATTR_TYPE::valueTable(ATTR_TYPE::STRING), $domainKey, $entityKey, $stringAttributeKey, ATTR_TYPE::randomValue(ATTR_TYPE::STRING));
            $integerValueKey = $valueModel->create(ATTR_TYPE::valueTable(ATTR_TYPE::STRING), $domainKey, $entityKey, $integerAttributeKey, ATTR_TYPE::randomValue(ATTR_TYPE::INTEGER));
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
            _ENTITY::ID,
            ATTR_TYPE::STRING,
            ATTR_TYPE::INTEGER,
            ATTR_TYPE::DECIMAL,
            ATTR_TYPE::DATETIME
        ]);
        $writer->insertAll($newValues);

        $decimalConfig  = new ConfigAttribute();
        $decimalConfig->setFields([
            _ATTR::NAME => ATTR_TYPE::DECIMAL,
            _ATTR::TYPE => ATTR_TYPE::DECIMAL
        ]);
        $decimalConfig->setGroupKey($groupKey);

        $datetimeConfig  = new ConfigAttribute();
        $datetimeConfig->setFields([
            _ATTR::NAME => ATTR_TYPE::DATETIME,
            _ATTR::TYPE => ATTR_TYPE::DATETIME
        ]);
        $datetimeConfig->setGroupKey($groupKey);

        $textConfig  = new ConfigAttribute();
        $textConfig->setFields([
            _ATTR::NAME => ATTR_TYPE::TEXT,
            _ATTR::TYPE => ATTR_TYPE::TEXT
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
                _ATTR::DOMAIN_ID, _ATTR::TYPE, _ATTR::NAME));

        $string = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::STRING,
            'name' => ATTR_TYPE::STRING
        ])->executeQuery()->fetchAssociative();

        $integer = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::INTEGER,
            'name' => ATTR_TYPE::INTEGER
        ])->executeQuery()->fetchAssociative();

        $decimal =  $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DECIMAL,
            'name' => ATTR_TYPE::DECIMAL
        ])->executeQuery()->fetchAssociative();

        $datetime = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::DATETIME,
            'name' => ATTR_TYPE::DATETIME
        ])->executeQuery()->fetchAssociative();

        $text = $q->setParameters([
            'domain' => $domainKey,
            'type' => ATTR_TYPE::TEXT,
            'name' => ATTR_TYPE::TEXT
        ])->executeQuery()->fetchAssociative();

        $this->assertIsArray($string);
        $this->assertIsArray($integer);
        $this->assertIsArray($decimal);
        $this->assertIsArray($datetime);
        $this->assertIsArray($text);

        // check attributes linked
        $pivotModel = new PivotModel();
        $stringPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $string[_ATTR::ID]);
        $integerPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $integer[_ATTR::ID]);
        $decimalPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $decimal[_ATTR::ID]);
        $datetimePivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $datetime[_ATTR::ID]);
        $textPivot = $pivotModel->findOne($domainKey, $setKey, $groupKey, $text[_ATTR::ID]);

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
            $entityKey = $entity[_ENTITY::ID];

            foreach($record as $attributeName => $value)
            {
                if($attributeName == _ENTITY::ID) continue;
                $attribute = match ($attributeName) {
                    ATTR_TYPE::STRING => $string,
                    ATTR_TYPE::INTEGER => $integer,
                    ATTR_TYPE::DECIMAL => $decimal,
                    ATTR_TYPE::DATETIME => $datetime,
                };

                $attrType = ATTR_TYPE::getCase($attribute[_ATTR::TYPE]);
                $valueTable = ATTR_TYPE::valueTable($attrType);
                $valueRecord = $valueModel->find($valueTable, $domainKey, $entityKey, $attribute[_ATTR::ID]);

                if($value == '')
                {
                    $this->assertFalse($valueRecord, "Unexpected value! Iteration:$iteration,Attribute:$attributeName");
                }
                else
                {
                    $this->assertIsArray($valueRecord);
                    $this->assertEquals($parser->parse($attrType, $value), $valueRecord[_VALUE::VALUE]);
                }
            }
            $iteration++;
        }
    }
}