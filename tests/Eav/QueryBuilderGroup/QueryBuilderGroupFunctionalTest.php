<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\QueryBuilderGroup;

use Drobotik\Eav\Enum\QB_CONDITION;
use Drobotik\Eav\QueryBuilder\QueryBuilderGroup;
use Drobotik\Eav\QueryBuilder\QueryBuilderAttributes;
use Drobotik\Eav\QueryBuilder\QueryBuilderRule;
use PHPUnit\Framework\TestCase;

class QueryBuilderGroupFunctionalTest extends TestCase
{
    private QueryBuilderGroup $group;

    public function setUp(): void
    {
        parent::setUp();
        $this->group = new QueryBuilderGroup();
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::setParent
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::getParent
     */
    public function parent()
    {
        $group = new QueryBuilderGroup();
        $this->group->setParent($group);
        $this->assertSame($group, $this->group->getParent());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::getAttributes
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::setAttributes
     */
    public function attributes()
    {
        $attributes = new QueryBuilderAttributes();
        $this->group->setAttributes($attributes);
        $this->assertSame($attributes, $this->group->getAttributes());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::__construct
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::setCondition
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::getCondition
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::resetCondition
     */
    public function condition()
    {
        $this->assertEquals(QB_CONDITION::AND, $this->group->getCondition());
        $this->group->setCondition(QB_CONDITION::OR);
        $this->assertEquals(QB_CONDITION::OR, $this->group->getCondition());
        $this->group->resetCondition();
        $this->assertEquals(QB_CONDITION::AND, $this->group->getCondition());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::getItems
     */
    public function items_default()
    {
        $this->assertEquals([], $this->group->getItems());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::getItems
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::appendRule
     */
    public function append_rule()
    {
        $rule = new QueryBuilderRule();
        $this->group->appendRule($rule);
        $items = $this->group->getItems();
        $this->assertCount(1, $items);
        $this->assertSame($rule, $items[0]);
        $this->assertSame($this->group, $items[0]->getGroup());
    }
    /**
     * @test
     *
     * @group functional
     *
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::getItems
     * @covers \Drobotik\Eav\QueryBuilder\QueryBuilderGroup::appendGroup
     */
    public function append_group()
    {
        $sub = new QueryBuilderGroup();
        $this->group->appendGroup($sub);
        $items = $this->group->getItems();
        $this->assertCount(1, $items);
        $this->assertSame($sub, $items[0]);
        $this->assertSame($this->group, $items[0]->getParent());
    }
}