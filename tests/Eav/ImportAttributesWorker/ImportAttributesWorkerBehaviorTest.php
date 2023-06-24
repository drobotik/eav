<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportAttributesWorker;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Factory\EavFactory;
use Drobotik\Eav\Import\Attributes\Config;
use Drobotik\Eav\Import\Attributes\ConfigAttribute;
use Drobotik\Eav\Import\Attributes\Validator;
use Drobotik\Eav\Import\Attributes\Worker;
use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Model\PivotModel;
use PHPUnit\Framework\TestCase;

class ImportAttributesWorkerBehaviorTest extends TestCase
{
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Attributes\Worker::validate
     */
    public function validate()
    {
        $config = new Config();
        $validator = $this->getMockBuilder(Validator::class)
            ->onlyMethods([
                'setConfig',
                'validate',
            ])
            ->getMock();
        $validator->expects($this->once())->method('setConfig')
            ->with($config);
        $validator->expects($this->once())->method('validate');
        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods([
                'getConfig',
                'getValidator',
            ])
            ->getMock();
        $worker->expects($this->once())->method('getConfig')
            ->willReturn($config);
        $worker->expects($this->once())->method('getValidator')
            ->willReturn($validator);

        $worker->validate();
    }
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Attributes\Worker::createAttribute
     */
    public function create_attribute()
    {
        $domainKey = 1;
        $setKey = 2;
        $groupKey = 3;
        $recordKey = 4;

        $fields = [
            _ATTR::NAME->column() => 'test',
            _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
        ];

        $attribute = $this->getMockBuilder(ConfigAttribute::class)
            ->onlyMethods(['getFields', 'getGroupKey'])->getMock();
        $attribute->expects($this->once())->method('getFields')->willReturn($fields);
        $attribute->expects($this->once())->method('getGroupKey')->willReturn($groupKey);

        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getDomainKey', 'getSetKey'])
            ->getMock();
        $container->expects($this->once())->method('getDomainKey')
            ->willReturn($domainKey);
        $container->expects($this->once())->method('getSetKey')
            ->willReturn($setKey);

        $attributeRecord = $this->getMockBuilder(AttributeModel::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getKey'])->getMock();
        $attributeRecord->expects($this->once())->method('getKey')->willReturn($recordKey);

        $factory = $this->getMockBuilder(EavFactory::class)
            ->onlyMethods(['createAttribute', 'createPivot'])->getMock();
        $factory->expects($this->once())->method('createAttribute')
            ->with(1, $fields)
            ->willReturn($attributeRecord);

        $factory->expects($this->once())->method('createPivot')
            ->with($domainKey, $setKey, $groupKey, $recordKey)
            ->willReturn(1);

        $pivotModel = $this->getMockBuilder(PivotModel::class)
            ->onlyMethods(['findOne'])
            ->getMock();
        $pivotModel->expects($this->once())->method('findOne')
            ->with($domainKey, $setKey, $groupKey, $recordKey)
            ->willReturn(false);

        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods([
                'makeEavFactory',
                'makePivotModel',
                'getContainer',
            ])
            ->getMock();

        $worker->expects($this->once())->method('makeEavFactory')->willReturn($factory);
        $worker->expects($this->once())->method('makePivotModel')->willReturn($pivotModel);
        $worker->expects($this->once())->method('getContainer')->willReturn($container);

        $worker->createAttribute($attribute);
    }
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Attributes\Worker::run
     */
    public function worker_run()
    {
        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['validate','createAttributes'])
            ->getMock();
        $worker->expects($this->once())->method('validate');
        $worker->expects($this->once())->method('createAttributes');
        $worker->run();
    }
}