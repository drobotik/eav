<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\PivotRepository;

use Drobotik\Eav\Model\PivotModel;
use Drobotik\Eav\Repository\PivotRepository;
use Tests\TestCase;

class PivotRepositoryFunctionalTest extends TestCase
{
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Repository\PivotRepository::createIfNotExist
     */
    public function create_if_not_exist() {
        $pivot = new PivotRepository();

        $domainKey = 1;
        $setKey = 2;
        $groupKey = 3;
        $attributeKey = 4;

        $record = $pivot->createIfNotExist($domainKey,$setKey,$groupKey,$attributeKey);
        $this->assertInstanceOf(PivotModel::class, $record);
        $this->assertEquals($domainKey, $record->getDomainKey());
        $this->assertEquals($setKey, $record->getAttrSetKey());
        $this->assertEquals($groupKey, $record->getGroupKey());
        $this->assertEquals($attributeKey, $record->getAttrKey());

        $newRecord = $pivot->createIfNotExist($domainKey,$setKey,$groupKey,$attributeKey);
        $this->assertEquals($record->getKey(), $newRecord->getKey());

        $newRecord = $pivot->createIfNotExist(5,6,7,8);
        $this->assertInstanceOf(PivotModel::class, $record);
        $this->assertEquals(5, $newRecord->getDomainKey());
        $this->assertEquals(6, $newRecord->getAttrSetKey());
        $this->assertEquals(7, $newRecord->getGroupKey());
        $this->assertEquals(8, $newRecord->getAttrKey());

        $this->assertNotEquals($record->getKey(), $newRecord->getKey());
    }

}