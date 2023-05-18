<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderManager;

use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use Drobotik\Eav\QueryBuilder\QueryBuilderManager;
use Tests\QueryBuilderTestCase;

class QueryBuilderManagerFunctionalTest extends QueryBuilderTestCase
{
    private QueryBuilderManager $manager;

    public function setUp(): void
    {
        parent::setUp();
        $this->manager = new QueryBuilderManager();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::getDomainKey
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::setDomainKey
     */
    public function domain_key()
    {
        $this->manager->setDomainKey(12);
        $this->assertSame(12 , $this->manager->getDomainKey());
    }

    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::getSetKey
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::setSetKey
     */
    public function set_key()
    {
        $this->manager->setSetKey(12);
        $this->assertSame(12 , $this->manager->getSetKey());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::setColumns
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::getColumns
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::isColumns
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::isColumn
     */
    public function columns()
    {
        $this->assertFalse($this->manager->isColumns());
        $this->assertFalse($this->manager->isColumn('test'));
        $this->manager->setColumns(['Tom', 'Jerry']);
        $this->assertEquals(['Tom', 'Jerry'], $this->manager->getColumns());
        $this->assertTrue($this->manager->isColumns());
        $this->assertFalse($this->manager->isColumn('test'));
        $this->assertTrue($this->manager->isColumn('Tom'));
        $this->assertFalse($this->manager->isColumn('tom'));
        $this->manager->setColumns([]);
        $this->assertFalse($this->manager->isColumns());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::getFilters
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::setFilters
     */
    public function filters()
    {
        $this->manager->setFilters([123]);
        $this->assertEquals([123], $this->manager->getFilters());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::getAttributesPivot
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::setAttributesPivot
     */
    public function attributes_pivot()
    {
        $pivot = new QueryBuilderAttributes();
        $this->manager->setAttributesPivot($pivot);
        $this->assertSame($pivot, $this->manager->getAttributesPivot());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::makeQuery
     */
    public function make_query()
    {
        $manager = $this->getMockBuilder(QueryBuilderManager::class)
            ->onlyMethods(['getBuilder'])
            ->getMock();
        $builder = $this->getQuery();
        $manager->expects($this->once())->method('getBuilder')
            ->willReturn($builder);
        $manager->setDomainKey(3);
        $manager->setSetKey(4);
        $expected = $this->getQuery()
            ->from(_ENTITY::table(), 'e')
            ->where(_ENTITY::DOMAIN_ID->column(), '=', 3)
            ->where(_ENTITY::ATTR_SET_ID->column(), '=', 4);
        $query = $manager->makeQuery();
        $this->assertEquals($expected->toSql(), $query->toSql());
    }

}