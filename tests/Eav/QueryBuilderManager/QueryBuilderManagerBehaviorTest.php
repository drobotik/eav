<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderManager;

use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Model\AttributeModel;
use Drobotik\Eav\QueryBuilder\QueryBuilder;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use Drobotik\Eav\QueryBuilder\QueryBuilderGroup;
use Drobotik\Eav\QueryBuilder\QueryBuilderManager;
use Drobotik\Eav\QueryBuilder\QueryBuilderParser;
use Illuminate\Database\Eloquent\Collection;
use Tests\QueryBuilderTestCase;

class QueryBuilderManagerBehaviorTest extends QueryBuilderTestCase
{
    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::markSelected
     */
    public function mark_selected()
    {
        $query = $this->getQuery();

        $attribute = new AttributeModel;
        $attribute->setName('name1');

        $pivot = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['setAttributeSelected'])
            ->getMock();
        $pivot->expects($this->once())->method('setAttributeSelected')
            ->with('name1');

        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->onlyMethods(['select'])
            ->getMock();
        $queryBuilder->expects($this->once())->method('select')
            ->with($query, 'name1');

        $manager = $this->getMockBuilder(QueryBuilderManager::class)
            ->onlyMethods(['makeQueryBuilder', 'getAttributesPivot'])
            ->getMock();

        $manager->expects($this->once())->method('makeQueryBuilder')
            ->willReturn($queryBuilder);
        $manager->expects($this->once())->method('getAttributesPivot')
            ->willReturn($pivot);

        $manager->markSelected($attribute, $query);
    }

    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::markJoined
     */
    public function mark_joined()
    {
        $query = $this->getQuery();

        $attribute = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['getKey', 'getName', 'getTypeEnum'])
            ->getMock();
        $attribute->expects($this->once())->method('getKey')
            ->willReturn(123);
        $attribute->expects($this->once())->method('getName')
            ->willReturn('name1');
        $attribute->expects($this->once())->method('getTypeEnum')
            ->willReturn(ATTR_TYPE::STRING);

        $pivot = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['setAttributeJoined'])
            ->getMock();
        $pivot->expects($this->once())->method('setAttributeJoined')
            ->with('name1');

        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)
            ->onlyMethods(['join'])
            ->getMock();
        $queryBuilder->expects($this->once())->method('join')
            ->with($query, ATTR_TYPE::STRING->valueTable(), 'name1', 123);

        $manager = $this->getMockBuilder(QueryBuilderManager::class)
            ->onlyMethods(['makeQueryBuilder', 'getAttributesPivot'])
            ->getMock();

        $manager->expects($this->once())->method('makeQueryBuilder')
            ->willReturn($queryBuilder);
        $manager->expects($this->once())->method('getAttributesPivot')
            ->willReturn($pivot);

        $manager->markJoined($attribute, $query);
    }


    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::setupAttribute
     */
    public function setup_attribute()
    {
        $query = $this->getQuery();
        $attribute = $this->getMockBuilder(AttributeModel::class)
            ->onlyMethods(['getName'])
            ->getMock();
        $attribute->expects($this->once())->method('getName')
            ->willReturn('name1');
        $manager = $this->getMockBuilder(QueryBuilderManager::class)
            ->onlyMethods(['isManualColumn', 'markSelected', 'markJoined'])
            ->getMock();
        $manager->expects($this->once())->method('isManualColumn')
            ->willReturn(true);
        $manager->expects($this->once())->method('markSelected')
            ->with($attribute, $query)
            ->willReturnCallback(fn($q) => $query->addSelect('name1'));
        $manager->expects($this->once())->method('markJoined')
            ->with($attribute, $query)
            ->willReturnCallback(fn($q) => $query->addSelect('name2'));

        $result = $manager->setupAttribute($attribute, $query);

        $expected = $this->getQuery()->select('name1', 'name2');
        $this->assertSame($expected->toSql(), $result->toSql());
    }

    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::makeAttributes
     */
    public function make_attributes()
    {
        $query = $this->getQuery();

        $attribute = new AttributeModel();

        $pivot = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['getAttributes'])
            ->getMock();
        $pivot->expects($this->once())->method('getAttributes')
            ->willReturn([$attribute]);

        $manager = $this->getMockBuilder(QueryBuilderManager::class)
            ->onlyMethods(['getAttributesPivot', 'setupAttribute'])
            ->getMock();

        $manager->expects($this->once())->method('getAttributesPivot')
            ->willReturn($pivot);

        $manager->expects($this->once())->method('setupAttribute')
            ->with($attribute, $query);

        $manager->makeAttributes($query);
    }

    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::initialize
     */
    public function initialize()
    {
        $query = $this->getQuery();
        $storedAttributes = new Collection();

        $pivot = $this->getMockBuilder(QueryBuilderAttributes::class)
            ->onlyMethods(['setAttributes'])
            ->getMock();
        $pivot->expects($this->once())->method('setAttributes')
            ->with($storedAttributes);

        $manager = $this->getMockBuilder(QueryBuilderManager::class)
            ->onlyMethods([
                'makeQueryBuilderAttributes',
                'setAttributesPivot',
                'getStoredAttributes',
                'makeQuery',
                'makeAttributes'
            ])
            ->getMock();

        $manager->expects($this->once())->method('makeQueryBuilderAttributes')
            ->willReturn($pivot);
        $manager->expects($this->once())->method('getStoredAttributes')
            ->willReturn($storedAttributes);
        $manager->expects($this->once())->method('setAttributesPivot')
            ->with($pivot);
        $manager->expects($this->once())->method('makeQuery')
            ->willReturn($query);
        $manager->expects($this->once())->method('makeAttributes')
            ->with($query)
            ->willReturn($query);

        $this->assertSame($query, $manager->initialize());
    }

    /**
     * @test
     *
     * @group behavior
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderManager::run
     */
    public function run_manager()
    {
        $filters = ['filters'];

        $query = $this->getQuery();

        $group = $this->getMockBuilder(QueryBuilderGroup::class)
            ->onlyMethods(['makeJoins', 'makeConditions'])
            ->getMock();
        $group->expects($this->once())->method('makeJoins')->with($query);
        $group->expects($this->once())->method('makeConditions')->with($query)
            ->willReturn($query);

        $parser = $this->getMockBuilder(QueryBuilderParser::class)
            ->onlyMethods(['setManager', 'parse'])
            ->getMock();
        $parser->expects($this->once())->method('parse')
            ->with($filters)
            ->willReturn($group);

        $manager = $this->getMockBuilder(QueryBuilderManager::class)
            ->onlyMethods([
                'initialize',
                'makeQueryBuilderParser',
                'getFilters'
            ])
            ->getMock();
        $parser->expects($this->once())->method('setManager')->with($manager);
        $manager->expects($this->once())->method('initialize')->willReturn($query);
        $manager->expects($this->once())->method('makeQueryBuilderParser')
            ->willReturn($parser);
        $manager->expects($this->once())->method('getFilters')->willReturn($filters);

        $this->assertSame($query, $manager->run());
    }
}