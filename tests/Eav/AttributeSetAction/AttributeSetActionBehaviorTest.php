<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSetAction;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\AttributeSetAction;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\EntityBag;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\Value\ValueManager;
use PHPUnit\Framework\TestCase;

class AttributeSetActionBehaviorTest extends TestCase
{
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\AttributeSetAction::initializeValueManager
     */
    public function initialize_value_manager() {
        $value = 'test';
        $bag = $this->getMockBuilder(EntityBag::class)
            ->onlyMethods(['hasField', 'getField'])
            ->getMock();
        $bag->expects($this->once())->method('hasField')
            ->with('email')
            ->willReturn(true);
        $bag->expects($this->once())->method('getField')
            ->with('email')
            ->willReturn($value);
        $attribute = $this->getMockBuilder(Attribute::class)
            ->onlyMethods(['getName'])
            ->getMock();
        $attribute->expects($this->once())->method('getName')->willReturn('email');
        $entity = $this->getMockBuilder(Entity::class)
            ->onlyMethods(['getBag'])
            ->getMock();
        $entity->expects($this->once())->method('getBag')->willReturn($bag);
        $attrSet = $this->getMockBuilder(AttributeSet::class)
            ->onlyMethods(['getEntity'])
            ->getMock();
        $attrSet->expects($this->once())->method('getEntity')->willReturn($entity);
        $valueManager = $this->getMockBuilder(ValueManager::class)
            ->onlyMethods(['setRuntime'])
            ->getMock();
        $valueManager->expects($this->once())->method('setRuntime')->with($value);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['find'])
            ->getMock();
        $strategy->expects($this->once())->method('find');
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['makeValueManager', 'makeValueAction', 'makeValueValidator', 'getAttributeSet', 'getAttribute', 'getStrategy', 'getValueManager'])
            ->getMock();
        $container->expects($this->once())->method('makeValueManager');
        $container->expects($this->once())->method('makeValueAction');
        $container->expects($this->once())->method('makeValueValidator');
        $container->expects($this->once())->method('getAttributeSet')->willReturn($attrSet);
        $container->expects($this->once())->method('getAttribute')->willReturn($attribute);
        $container->expects($this->once())->method('getStrategy')->willReturn($strategy);
        $container->expects($this->once())->method('getValueManager')->willReturn($valueManager);
        $action = $this->getMockBuilder(AttributeSetAction::class)
            ->onlyMethods(['getAttributeContainer'])
            ->getMock();
        $action->expects($this->once())->method('getAttributeContainer')->willReturn($container);
        $action->initializeValueManager();
    }
    /**
     * @test
     * @group behavior
     * @covers \Kuperwood\Eav\AttributeSetAction::initialize
     */
    public function initialize() {
        $attributeRecord = [123];
        $action = $this->getMockBuilder(AttributeSetAction::class)
            ->onlyMethods(['initializeAttribute','initializeStrategy','initializeValueManager'])
            ->getMock();
        $action->expects($this->once())->method('initializeValueManager');
        $attribute = new Attribute();
        $action->expects($this->once())->method('initializeAttribute')
            ->with($attributeRecord)->willReturn($attribute);
        $action->expects($this->once())->method('initializeStrategy')
            ->with($attribute);
        $action->initialize($attributeRecord);
    }
}