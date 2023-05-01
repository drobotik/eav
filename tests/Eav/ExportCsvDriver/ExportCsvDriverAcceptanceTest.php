<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ExportCsvDriver;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Enum\EXPORT;
use Drobotik\Eav\Export\ExportCsvDriver;
use Drobotik\Eav\Result\Result;
use Tests\TestCase;

class ExportCsvDriverAcceptanceTest extends TestCase
{
    /**
     * @test
     *
     * @group acceptance
     *
     * @covers \Drobotik\Eav\Export\ExportCsvDriver::run
     */
    public function results()
    {
        $driver = new ExportCsvDriver();
        $domainRecord = $this->eavFactory->createDomain();
        $set = $this->eavFactory->createAttributeSet();
        $group1 = $this->eavFactory->createGroup($set);
        $group2 = $this->eavFactory->createGroup($set);
        $rows = [
            [ATTR_TYPE::STRING->value(),ATTR_TYPE::INTEGER->value(),ATTR_TYPE::DECIMAL->value(),ATTR_TYPE::DATETIME->value(),ATTR_TYPE::TEXT->value()]
        ];
        for($i=0; $i < 3; $i++) {
            $string = ATTR_TYPE::STRING->randomValue();
            $integer = ATTR_TYPE::INTEGER->randomValue();
            $decimal = ATTR_TYPE::DECIMAL->randomValue();
            $datetime = ATTR_TYPE::DATETIME->randomValue($i);
            $text = ATTR_TYPE::TEXT->randomValue();
            $rows[] = [$string, $integer, $decimal, $datetime, $text];
            $config = [
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::STRING->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group1->getKey(),
                    ATTR_FACTORY::VALUE->field() => $string
                ],
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::INTEGER->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group1->getKey(),
                    ATTR_FACTORY::VALUE->field() => $integer
                ],
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::DECIMAL->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group1->getKey(),
                    ATTR_FACTORY::VALUE->field() => $decimal
                ],
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::DATETIME->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group2->getKey(),
                    ATTR_FACTORY::VALUE->field() => $datetime
                ],
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::TEXT->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group2->getKey(),
                    ATTR_FACTORY::VALUE->field() => $text
                ]
            ];
            $this->eavFactory->createEavEntity($config, $domainRecord, $set);
        }

        $path = dirname(__DIR__, 2) . '/temp/csv.csv';
        $config = [
            EXPORT::PATH->field() => $path,
            EXPORT::DOMAIN_KEY->field() => $domainRecord->getKey(),
            EXPORT::SET_KEY->field() => $set->getKey()
        ];
        $driver->setData($config);
        $result = $driver->run();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertFileExists($path);
        $fp = fopen($path, 'r');
        $data = array();
        while (($row = fgetcsv($fp)) !== false) {
            $data[] = $row;
        }
        fclose($fp);
        $this->assertEquals($rows, $data);
    }

}
