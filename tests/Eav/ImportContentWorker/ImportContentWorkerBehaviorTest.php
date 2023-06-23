<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportContentWorker;

use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Content\AttributeSet;
use Drobotik\Eav\Import\Content\Value;
use Drobotik\Eav\Import\Content\ValueSet;
use Drobotik\Eav\Import\Content\Worker;
use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Repository\ValueRepository;
use PHPUnit\Framework\TestCase;

class ImportContentWorkerBehaviorTest extends TestCase
{
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::createEntities
     */
    public function create_entities()
    {
        $domainKey = 1;
        $setKey = 2;
        $values = [1,2,3];
        $serviceKey = 456;
        $lineIndex = 2;

        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getDomainKey', 'getSetKey'])->getMock();
        $container->method('getDomainKey')->willReturn($domainKey);
        $container->method('getSetKey')->willReturn($setKey);

        $entityModel = $this->getMockBuilder(EntityModel::class)
            ->onlyMethods(['getServiceKey', 'bulkCreate', 'getByServiceKey'])
            ->getMock();
        $entityModel->expects($this->once())->method('getServiceKey')
            ->willReturn($serviceKey);
        $entityModel->expects($this->once())->method('bulkCreate')
            ->with($lineIndex, $domainKey, $setKey, $serviceKey);
        $entityModel->expects($this->once())->method('getByServiceKey')
            ->with($serviceKey)
            ->willReturn($values);

        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['getContainer', 'getLineIndex', 'makeEntityModel'])->getMock();
        $worker->method('getContainer')->willReturn($container);
        $worker->method('makeEntityModel')->willReturn($entityModel);
        $worker->method('getLineIndex')->willReturn($lineIndex);

        $this->assertEquals($values, $worker->createEntities());
    }
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::processNewEntities
     */
    public function process_new_entities()
    {
        $domainKey = 1;
        $entity1Key = 10;
        $entity2Key = 20;

        $entity1 = [
            _ENTITY::ID->column() => $entity1Key,
            _ENTITY::DOMAIN_ID->column() => $domainKey
        ];
        $entity2 = [
            _ENTITY::ID->column() => $entity2Key,
            _ENTITY::DOMAIN_ID->column() => $domainKey
        ];
        $collection = [$entity1, $entity2];

        $value1 = $this->getMockBuilder(Value::class)
            ->onlyMethods(['setEntityKey', 'getLineIndex','isEmptyValue'])->getMock();
        $value1->expects($this->once())->method('isEmptyValue')->willReturn(false);
        $value1->expects($this->once())->method('getLineIndex')->willReturn(1);
        $value1->expects($this->once())->method('setEntityKey')
            ->with($entity1Key);
        $value2 = $this->getMockBuilder(Value::class)
            ->onlyMethods(['setEntityKey', 'getLineIndex','isEmptyValue'])->getMock();
        $value1->expects($this->once())->method('isEmptyValue')->willReturn(false);
        $value2->expects($this->once())->method('getLineIndex')->willReturn(2);
        $value2->expects($this->once())->method('setEntityKey')
            ->with($entity2Key);

        $valueSet = $this->getMockBuilder(ValueSet::class)
            ->onlyMethods(['forNewEntities'])->getMock();
        $valueSet->expects($this->once())->method('forNewEntities')
            ->willReturn([$value1, $value2]);

        $bulkCreateSet = $this->getMockBuilder(ValueSet::class)
            ->onlyMethods(['appendValue'])->getMock();
        $bulkCreateSet->expects($this->exactly(2))
            ->method('appendValue')
            ->withConsecutive([$value1], [$value2]);

        $valueRepo = $this->getMockBuilder(ValueRepository::class)
            ->onlyMethods(['bulkCreate'])->getMock();
        $valueRepo->method('bulkCreate')
            ->with($bulkCreateSet , $domainKey);

        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getDomainKey', 'getSetKey'])->getMock();
        $container->method('getDomainKey')->willReturn($domainKey);

        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods([
                'getContainer',
                'makeValueRepository',
                'getValueSet',
                'makeBulkValuesSet',
                'createEntities'
            ])->getMock();
        $worker->method('getContainer')->willReturn($container);
        $worker->method('makeValueRepository')->willReturn($valueRepo);
        $worker->method('getValueSet')->willReturn($valueSet);
        $worker->method('makeBulkValuesSet')->willReturn($bulkCreateSet);
        $worker->method('createEntities')->willReturn($collection);

        $worker->processNewEntities();
    }

    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::processExistingEntities
     */
    public function process_existing_entities()
    {
        $domainKey = 1;
        $entity1Key = 10;
        $entity2Key = 20;
        $attribute1name = "name";
        $attribute2name = "type";
        $attribute3name = "description";
        $attribute1key = 5;
        $attribute2key = 15;
        $attribute3key = 21;
        $attribute1type = ATTR_TYPE::INTEGER;
        $attribute2type = ATTR_TYPE::TEXT;
        $attribute3type = ATTR_TYPE::TEXT;
        $content1 = "content1";
        $content2 = "content2";
        $content3 = "";

        $attribute1 = $this->getMockBuilder(AttributeModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getKey', 'getTypeEnum'])
            ->getMock();
        $attribute1->expects($this->once())->method('getKey')
            ->willReturn($attribute1key);
        $attribute1->expects($this->once())->method('getTypeEnum')
            ->willReturn($attribute1type);

        $attribute2 = $this->getMockBuilder(AttributeModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getKey', 'getTypeEnum'])
            ->getMock();
        $attribute2->expects($this->once())->method('getKey')
            ->willReturn($attribute2key);
        $attribute2->expects($this->once())->method('getTypeEnum')
            ->willReturn($attribute2type);

        $attribute3 = $this->getMockBuilder(AttributeModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getKey', 'getTypeEnum'])
            ->getMock();
        $attribute3->expects($this->once())->method('getKey')
            ->willReturn($attribute3key);
        $attribute3->expects($this->once())->method('getTypeEnum')
            ->willReturn($attribute3type);

        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getAttribute'])->getMock();
        $attrSet->expects($this->exactly(3))->method('getAttribute')
            ->withConsecutive([$attribute1name], [$attribute2name], [$attribute3name])
            ->willReturn($attribute1, $attribute2, $attribute3);

        $value1 = $this->getMockBuilder(Value::class)
            ->onlyMethods(['getEntityKey', 'getAttributeName', 'getValue'])->getMock();
        $value1->expects($this->once())->method('getEntityKey')
            ->willReturn($entity1Key);
        $value1->expects($this->once())->method('getAttributeName')
            ->willReturn($attribute1name);
        $value1->expects($this->once())->method('getValue')
            ->willReturn($content1);
        $value2 = $this->getMockBuilder(Value::class)
            ->onlyMethods(['getEntityKey', 'getAttributeName', 'getValue'])->getMock();
        $value2->expects($this->once())->method('getEntityKey')
            ->willReturn($entity2Key);
        $value2->expects($this->once())->method('getAttributeName')
            ->willReturn($attribute2name);
        $value2->expects($this->once())->method('getValue')
            ->willReturn($content2);
        $value3 = $this->getMockBuilder(Value::class)
            ->onlyMethods(['getEntityKey', 'getAttributeName', 'getValue'])->getMock();
        $value3->expects($this->once())->method('getEntityKey')
            ->willReturn($entity1Key);
        $value3->expects($this->once())->method('getAttributeName')
            ->willReturn($attribute3name);
        $value3->expects($this->once())->method('getValue')
            ->willReturn($content3);

        $valueSet = $this->getMockBuilder(ValueSet::class)
            ->onlyMethods(['forExistingEntities'])->getMock();
        $valueSet->expects($this->once())->method('forExistingEntities')
            ->willReturn([$value1, $value2, $value3]);

        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getDomainKey'])->getMock();
        $container->method('getDomainKey')->willReturn($domainKey);

        $valueRepo = $this->getMockBuilder(ValueRepository::class)
            ->onlyMethods(['updateOrCreate', 'destroy'])->getMock();
        $valueRepo->expects($this->exactly(2))->method('updateOrCreate')
            ->withConsecutive(
                [$domainKey, $entity1Key, $attribute1key, $attribute1type, $content1],
                [$domainKey, $entity2Key, $attribute2key, $attribute2type, $content2]
            );
        $valueRepo->expects($this->once())->method('destroy')
            ->with($domainKey, $entity1Key, $attribute3key, $attribute3type);

        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods([
                'getContainer',
                'getValueSet',
                'getAttributeSet',
                'makeValueRepository'
            ])->getMock();
        $worker->method('getContainer')->willReturn($container);
        $worker->method('getValueSet')->willReturn($valueSet);
        $worker->method('getAttributeSet')->willReturn($attrSet);
        $worker->method('makeValueRepository')->willReturn($valueRepo);

        $worker->processExistingEntities();
    }

    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::run
     */
    public function run_worker()
    {
        $chunk1 = [
            ["name" => "Tom", "type" => "cat"],
            ["name" => "Jerry", "type" => "mouse"]
        ];
        $chunk2 = [
            ["name" => "Donald", "type" => "duck"],
            ["name" => "Lo", "type" => "rabbit"]
        ];
        $driver = $this->getMockBuilder(CsvDriver::class)
            ->onlyMethods(['getChunk'])->getMock();
        $driver
            ->expects($this->exactly(3))
            ->method('getChunk')
            ->will($this->onConsecutiveCalls($chunk1, $chunk2, null));

        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getDriver'])->getMock();
        $container->method('getDriver')->willReturn($driver);

        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['initialize'])->getMock();
        $attrSet->expects($this->once())->method('initialize');

        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods([
                'getContainer',
                'getAttributeSet',
                'parseChunk',
                'processExistingEntities',
                'processNewEntities',
                'cleanup',
                'resetLineIndex'
            ])->getMock();
        $worker->method('getContainer')->willReturn($container);
        $worker->method('getAttributeSet')->willReturn($attrSet);
        $worker->expects($this->exactly(2))
            ->method('parseChunk')
            ->withConsecutive([$chunk1], [$chunk2]);
        $worker->expects($this->exactly(2))->method('processExistingEntities');
        $worker->expects($this->exactly(2))->method('processNewEntities');
        $worker->expects($this->exactly(2))->method('cleanup');
        $worker->expects($this->exactly(2))->method('resetLineIndex');
        $worker->run();
    }

    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Content\Worker::cleanup
     */
    public function cleanup()
    {
        $valueSet = $this->getMockBuilder(ValueSet::class)
            ->onlyMethods(['resetValues'])->getMock();
        $valueSet->expects($this->once())->method('resetValues');
        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['getValueSet',])->getMock();
        $worker->method('getValueSet')->willReturn($valueSet);
        $worker->cleanup();
    }
}