<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ExportManager;

use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\Enum\QB_CONFIG;
use Drobotik\Eav\Enum\QB_OPERATOR;
use Drobotik\Eav\Export\ExportManager;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;
use SplFileObject;
use Tests\QueryingDataTestCase;

class ExportManagerAcceptanceTest extends QueryingDataTestCase
{
    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\Export\ExportManager::run
     */
    public function export_query_builder()
    {
        $file = new SplFileObject(dirname(__DIR__, 2) . '/Data/csv.csv','w');
        $writer = Writer::createFromFileObject($file);
        $driver = new CsvDriver();
        $driver->setWriter($writer);

        $filters = [
            QB_CONFIG::CONDITION => QB_CONDITION::AND,
            QB_CONFIG::RULES => [
                [
                    QB_CONFIG::NAME => ATTR_TYPE::DECIMAL,
                    QB_CONFIG::OPERATOR => QB_OPERATOR::LESS,
                    QB_CONFIG::VALUE => 10000
                ],
                [
                    QB_CONFIG::CONDITION => QB_CONDITION::OR,
                    QB_CONFIG::RULES => [
                        [
                            QB_CONFIG::NAME => ATTR_TYPE::STRING,
                            QB_CONFIG::OPERATOR => QB_OPERATOR::CONTAINS,
                            QB_CONFIG::VALUE => 'sit quisquam'
                        ],
                        [
                            QB_CONFIG::NAME => ATTR_TYPE::STRING,
                            QB_CONFIG::OPERATOR => QB_OPERATOR::EQUAL,
                            QB_CONFIG::VALUE => 'et dolores'
                        ]
                    ],
                ]
            ],
        ];

        $domainKey = 1;
        $setKey = 1;
        $columns = [ATTR_TYPE::STRING, ATTR_TYPE::DECIMAL];
        $manager = new ExportManager();
        $manager->setDriver($driver);
        $manager->run($domainKey, $setKey, $filters, $columns);

        $file = new SplFileObject(dirname(__DIR__, 2) . '/Data/csv.csv','r');
        $reader = Reader::createFromFileObject($file);

        $stmt = new Statement();
        $records = $stmt->process($reader);

        $this->assertEquals(4, $records->count());
        $output = [];
        foreach ($records as $record)
        {
            $output[] = $record;
        }

        $this->assertEquals([
            [_ENTITY::ID,ATTR_TYPE::STRING,ATTR_TYPE::DECIMAL],
            ['1822','et dolores','170.359'],
            ['18795','sit quisquam','3685.969'],
            ['19738','sit quisquam','180.63']
        ], $output);
    }
}