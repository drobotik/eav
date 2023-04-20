<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\Strategy;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Strategy;
use Drobotik\Eav\Value\ValueAction;
use Drobotik\Eav\Value\ValueManager;
use Drobotik\Eav\Value\ValueValidator;
use PHPUnit\Framework\TestCase;

class StrategyBehaviorTest extends TestCase
{
    private Strategy $strategy;
    public function setUp(): void
    {
        parent::setUp();
        $this->strategy = new Strategy();
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Strategy::create
     */
    public function create_action() {
        $valueAction = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['create'])
            ->getMock();
        $valueAction->expects($this->once())
            ->method('create')
            ->willReturn((new Result())->created());
        $container = new AttributeContainer();
        $container->setValueAction($valueAction);
        $this->strategy->setAttributeContainer($container);

        $result = $this->strategy->create();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Strategy::find
     */
    public function find_action() {
        $valueAction = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['find'])
            ->getMock();
        $valueAction->expects($this->once())
            ->method('find')
            ->willReturn((new Result())->found());
        $container = new AttributeContainer();
        $container->setValueAction($valueAction);
        $this->strategy->setAttributeContainer($container);

        $result = $this->strategy->find();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::FOUND->code(), $result->getCode());
        $this->assertEquals(_RESULT::FOUND->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Strategy::update
     */
    public function update_action_updated() {
        $valueAction = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['update'])
            ->getMock();
        $valueAction->expects($this->once())
            ->method('update')
            ->willReturn((new Result())->updated());
        $valueManager = $this->getMockBuilder(ValueManager::class)
            ->onlyMethods(['hasKey'])
            ->getMock();
        $valueManager->expects($this->once())
            ->method('hasKey')
            ->willReturn(true);
        $container = new AttributeContainer();
        $container->setValueAction($valueAction)
            ->setValueManager($valueManager);
        $this->strategy->setAttributeContainer($container);
        $result = $this->strategy->update();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Strategy::update
     */
    public function update_action_created() {
        $valueAction = $this->getMockBuilder(ValueAction::class)
            ->onlyMethods(['create'])
            ->getMock();
        $valueAction->expects($this->once())
            ->method('create')
            ->willReturn((new Result())->created());
        $container = new AttributeContainer();
        $container->setValueAction($valueAction)
            ->makeValueManager();
        $this->strategy->setAttributeContainer($container);
        $result = $this->strategy->update();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Strategy::delete
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
        $this->assertEquals(_RESULT::DELETED->code(), $result->getCode());
        $this->assertEquals(_RESULT::DELETED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Strategy::save
     */
    public function save_action_create()
    {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $container->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['create'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('create')
            ->willReturn((new Result())->created());
        $strategy->setAttributeContainer($container);
        $result = $strategy->save();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::CREATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::CREATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Strategy::save
     */
    public function save_action_update()
    {
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getKey'])
            ->getMock();
        $entity->expects($this->once())
            ->method('getKey')
            ->willReturn(1);
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $attrSet->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getAttributeSet'])
            ->getMock();
        $container->expects($this->once())
            ->method('getAttributeSet')
            ->willReturn($attrSet);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['update'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('update')
            ->willReturn((new Result())->updated());
        $strategy->setAttributeContainer($container);
        $result = $strategy->save();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::UPDATED->code(), $result->getCode());
        $this->assertEquals(_RESULT::UPDATED->message(), $result->getMessage());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Strategy::validate
     */
    public function validate_fails_action() {
        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fails', 'errors'])
            ->getMock();
        $validator->expects($this->once())
            ->method('fails')
            ->willReturn(true);
        $messageBag = new MessageBag();
        $messageBag->add('test', 'test');
        $validator->expects($this->once())
            ->method('errors')
            ->willReturn($messageBag);
        $valueValidator = $this->getMockBuilder(ValueValidator::class)
            ->onlyMethods(['getValidator'])
            ->getMock();
        $valueValidator->expects($this->once())
            ->method('getValidator')
            ->willReturn($validator);
        $container = new AttributeContainer();
        $container->setValueValidator($valueValidator);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['getAttributeContainer'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('getAttributeContainer')
            ->willReturn($container);
        $result = $strategy->validate();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_FAILS->code(), $result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_FAILS->message(), $result->getMessage());
        $this->assertSame($messageBag, $result->getData());
    }
    /**
     * @test
     * @group behavior
     * @covers \Drobotik\Eav\Strategy::validate
     */
    public function validate_passed_action() {
        $validator = $this->getMockBuilder(Validator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['fails', 'errors'])
            ->getMock();
        $validator->expects($this->once())
            ->method('fails')
            ->willReturn(false);
        $valueValidator = $this->getMockBuilder(ValueValidator::class)
            ->onlyMethods(['getValidator'])
            ->getMock();
        $valueValidator->expects($this->once())
            ->method('getValidator')
            ->willReturn($validator);
        $container = new AttributeContainer();
        $container->setValueValidator($valueValidator);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['getAttributeContainer'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('getAttributeContainer')
            ->willReturn($container);
        $result = $strategy->validate();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals(_RESULT::VALIDATION_PASSED->code(), $result->getCode());
        $this->assertEquals(_RESULT::VALIDATION_PASSED->message(), $result->getMessage());
        $this->assertNull($result->getData());
    }
}