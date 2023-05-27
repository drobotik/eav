<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\ImportContentAttributeSet;

use Drobotik\Eav\Driver\CsvDriver;
use Drobotik\Eav\Import\Content\AttributeSet;
use Drobotik\Eav\Import\Content\Worker;
use Drobotik\Eav\Import\ImportContainer;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\Repository\AttributeRepository;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class ImportContentAttributeSetTest extends TestCase
{
    private AttributeSet $set;
    public function setUp(): void
    {
        parent::setUp();
        $this->set = new AttributeSet();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\AttributeSet::getWorker
     * @covers \Drobotik\Eav\Import\Content\AttributeSet::setWorker
     */
    public function worker()
    {
        $worker = new Worker();
        $this->set->setWorker($worker);
        $this->assertSame($worker, $this->set->getWorker());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Import\Content\AttributeSet::appendAttribute
     * @covers \Drobotik\Eav\Import\Content\AttributeSet::getAttribute
     * @covers \Drobotik\Eav\Import\Content\AttributeSet::hasAttribute
     */
    public function attributes()
    {
        $this->assertFalse($this->set->hasAttribute('test'));
        $attribute = new AttributeModel();
        $attribute->setName('test');
        $this->set->appendAttribute($attribute);
        $this->assertTrue($this->set->hasAttribute('test'));
        $this->assertSame($attribute, $this->set->getAttribute('test'));
    }
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\Import\Content\AttributeSet::initialize
     */
    public function initialize()
    {
        $domainKey = 123;
        $setKey = 456;
        $header = ['test'];
        $attribute1 = new AttributeModel();
        $attribute1->setName('test');
        $attribute2 = new AttributeModel();
        $attribute2->setName('other');
        $attributes = new Collection([$attribute1, $attribute2]);

        $driver = $this->getMockBuilder(CsvDriver::class)
            ->onlyMethods(['getHeader'])->getMock();
        $driver->expects($this->once())->method('getHeader')->willReturn($header);
        $container = $this->getMockBuilder(ImportContainer::class)
            ->onlyMethods(['getDriver', 'getDomainKey', 'getSetKey'])->getMock();
        $container->expects($this->once())->method('getDriver')->willReturn($driver);
        $container->expects($this->once())->method('getDomainKey')->willReturn($domainKey);
        $container->expects($this->once())->method('getSetKey')->willReturn($setKey);
        $worker = $this->getMockBuilder(Worker::class)
            ->onlyMethods(['getContainer'])->getMock();
        $worker->expects($this->once())->method('getContainer')->willReturn($container);

        $repository = $this->getMockBuilder(AttributeRepository::class)
            ->onlyMethods(['getLinked'])->getMock();
        $repository->expects($this->once())->method('getLinked')
            ->with($domainKey, $setKey)
            ->willReturn($attributes);

        $set = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getWorker','makeAttributeRepository', 'appendAttribute'])
            ->getMock();

        $set->expects($this->once())->method('getWorker')->willReturn($worker);
        $set->expects($this->once())->method('makeAttributeRepository')->willReturn($repository);
        $set->expects($this->once())->method('appendAttribute')
            ->with($attribute1);

        $set->initialize();
    }
}