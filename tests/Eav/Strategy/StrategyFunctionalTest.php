<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Strategy;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\Value\ValueAction;
use Kuperwood\Eav\Value\ValueManager;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\StrategyFixture;
use Tests\Fixtures\ValueActionFixture;

class StrategyFunctionalTest extends TestCase
{
    private Strategy $strategy;
    public function setUp(): void
    {
        parent::setUp();
        $this->strategy = new Strategy();
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::afterCreate
     * @covers \Kuperwood\Eav\Strategy::beforeCreate
     * @covers \Kuperwood\Eav\Strategy::beforeUpdate
     * @covers \Kuperwood\Eav\Strategy::afterUpdate
     * @covers \Kuperwood\Eav\Strategy::beforeDelete
     * @covers \Kuperwood\Eav\Strategy::afterDelete
     */
    public function empty_methods()
    {
        $this->assertTrue(method_exists($this->strategy, 'afterCreate'));
        $this->assertTrue(method_exists($this->strategy, 'beforeCreate'));
        $this->assertTrue(method_exists($this->strategy, 'beforeUpdate'));
        $this->assertTrue(method_exists($this->strategy, 'afterUpdate'));
        $this->assertTrue(method_exists($this->strategy, 'beforeDelete'));
        $this->assertTrue(method_exists($this->strategy, 'afterDelete'));

        $this->strategy->afterCreate();
        $this->strategy->beforeCreate();
        $this->strategy->beforeUpdate();
        $this->strategy->afterUpdate();
        $this->strategy->beforeDelete();
        $this->strategy->afterDelete();
    }

    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::rules
     */
    public function rules()
    {
        $this->assertNull($this->strategy->rules());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::create
     */
    public function create_action_order() {
        $strategy = new StrategyFixture;
        $valueAction = new ValueActionFixture();
        $container = new AttributeContainer();
        $container->setStrategy($strategy)
            ->setValueAction($valueAction);
        $strategy->create();
        $this->assertEquals(['beforeCreate', 'createValue', 'afterCreate'], $strategy->lifecycle);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::isCreate
     */
    public function is_create() {
        $this->assertTrue($this->strategy->isCreate());
        $this->strategy->create = false;
        $this->assertFalse($this->strategy->isCreate());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::create
     */
    public function no_create() {
        $this->strategy->create = false;
        $valueManager = new ValueManager();
        $valueManager->setKey(123);
        $container = new AttributeContainer();
        $container->setValueManager($valueManager)
            ->makeValueAction();
        $this->strategy->setAttributeContainer($container);

        $result = $this->strategy->create();

        $this->assertFalse($valueManager->isRuntime());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_ALLOWED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_ALLOWED), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::update
     */
    public function update_action_order() {
        $strategy = new StrategyFixture();
        $valueAction = new ValueActionFixture();
        $container = new AttributeContainer();
        $container->setStrategy($strategy)
            ->setValueAction($valueAction)
            ->makeValueManager();
        $strategy->update();
        $this->assertEquals(['beforeUpdate', 'createValue', 'afterUpdate'], $strategy->lifecycle);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::update
     */
    public function update_action_order_when_existing_value_key() {
        $strategy = new StrategyFixture();
        $valueAction = new ValueActionFixture();
        $valueManager = new ValueManager();
        $valueManager->setKey(123);
        $container = new AttributeContainer();
        $container->setValueManager($valueManager)
            ->setStrategy($strategy)
            ->setValueAction($valueAction);

        $strategy->update();

        $this->assertEquals(['beforeUpdate', 'updateValue', 'afterUpdate'], $strategy->lifecycle);
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::isUpdate
     */
    public function is_update() {
        $this->assertTrue($this->strategy->isUpdate());
        $this->strategy->update = false;
        $this->assertFalse($this->strategy->isUpdate());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::update
     */
    public function no_update() {
        $this->strategy->update = false;
        $valueManager = new ValueManager();
        $valueManager->setKey(123);
        $container = new AttributeContainer();
        $container->setValueManager($valueManager)
            ->makeValueAction();
        $this->strategy->setAttributeContainer($container);

        $result = $this->strategy->update();

        $this->assertFalse($valueManager->isRuntime());
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::NOT_ALLOWED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::NOT_ALLOWED), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::delete
     */
    public function delete_action() {
        $strategy = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['delete'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('delete')
            ->willReturn((new Result())->deleted());
        $container = new AttributeContainer();
        $container->setValueAction($strategy);
        $this->strategy->setAttributeContainer($container);

        $result = $this->strategy->delete();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::DELETED, $result->getCode());
        $this->assertEquals(_RESULT::message(_RESULT::DELETED), $result->getMessage());
    }
    /**
     * @test
     * @group functional
     * @covers \Kuperwood\Eav\Strategy::delete
     */
    public function delete_action_order() {
        $strategy = new StrategyFixture;
        $valueAction = new ValueActionFixture;
        $container = new AttributeContainer();
        $container->setStrategy($strategy)
            ->setValueAction($valueAction);
        $strategy->setAttributeContainer($container);
        $strategy->delete();
        $this->assertEquals(['beforeDelete', 'deleteValue', 'afterDelete'], $strategy->lifecycle);
    }
}