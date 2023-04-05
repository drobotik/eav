<?php

namespace Tests\Unit\Entity;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\EntityAction;
use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\ValueManager;
use Tests\TestCase;

class EntityActionTest extends TestCase
{
    /** @test */
    public function save_value() {
        $value = 'test';
        $result = (new Result())->created();
        $valueManager = $this->getMockBuilder(ValueManager::class)
            ->onlyMethods(['setValue'])
            ->getMock();
        $valueManager->expects($this->once())
            ->method('setValue')
            ->with($value);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['save'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('save')
            ->willReturn($result);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getValueManager', 'getStrategy'])
            ->getMock();
        $container->expects($this->once())
            ->method('getValueManager')
            ->willReturn($valueManager);
        $container->expects($this->once())
            ->method('getStrategy')
            ->willReturn($strategy);
        $action = $this->getMockBuilder(EntityAction::class)
            ->onlyMethods(['getAttributeContainer'])
            ->getMock();
        $action->expects($this->once())
            ->method('getAttributeContainer')
            ->willReturn($container);
        $this->assertSame($result, $action->saveValue($value));
    }

    /** @test */
    public function validate_field_fails() {
        $result = (new Result())->validationFails();
        $result->setData(['email' => 'email is invalid']);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['validate'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('validate')
            ->willReturn($result);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getStrategy'])
            ->getMock();
        $container->expects($this->once())
            ->method('getStrategy')
            ->willReturn($strategy);
        $action = $this->getMockBuilder(EntityAction::class)
            ->onlyMethods(['getAttributeContainer'])
            ->getMock();
        $action->expects($this->once())
            ->method('getAttributeContainer')
            ->willReturn($container);
        $this->assertSame($result->getData(), $action->validateField());
    }

    /** @test */
    public function validate_field_ok() {
        $result = (new Result())->validationPassed();
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['validate'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('validate')
            ->willReturn($result);
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['getStrategy'])
            ->getMock();
        $container->expects($this->once())
            ->method('getStrategy')
            ->willReturn($strategy);
        $action = $this->getMockBuilder(EntityAction::class)
            ->onlyMethods(['getAttributeContainer'])
            ->getMock();
        $action->expects($this->once())
            ->method('getAttributeContainer')
            ->willReturn($container);
        $this->assertNull($action->validateField());
    }

}