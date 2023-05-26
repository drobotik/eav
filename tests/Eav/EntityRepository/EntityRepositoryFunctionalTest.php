<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\EntityRepository;

use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Model\EntityModel;
use Drobotik\Eav\Repository\EntityRepository;
use Tests\TestCase;


class EntityRepositoryFunctionalTest extends TestCase
{

    private EntityRepository $repository;

    public function setUp(): void
    {
        $this->repository = new EntityRepository();
        parent::setUp();
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Repository\EntityRepository::getServiceKey
     */
    public function serviceKey()
    {
        $key = 123;
        $factory = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateServiceKey', 'isServiceKey'])
            ->getMock();
        $factory->expects($this->once())->method('generateServiceKey')
            ->willReturn($key);
        $factory->expects($this->once())->method('isServiceKey')
            ->willReturn(false);
        $this->assertEquals($key, $factory->getServiceKey());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\Repository\EntityRepository::bulkCreate
     */
    public function bulk_create_entity_records() {
        $serviceKey = 123;
        $this->repository->bulkCreate(100, 2, 3, $serviceKey);
        $entities = EntityModel::query()->where(_ENTITY::SERVICE_KEY->column(), $serviceKey)->get();
        $this->assertEquals(100, $entities->count());
        /** @var EntityModel $entity */
        foreach($entities as $index => $entity) {
            $this->assertEquals(2, $entity->getDomainKey(), "Iteration:".$index);
            $this->assertEquals(3, $entity->getAttrSetKey(), "Iteration:".$index);
        }
    }


}