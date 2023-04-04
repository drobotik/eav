<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\AttributeSetAction;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Strategy;
use Tests\TestCase;

class AttributeSetActionTest extends TestCase
{
    private AttributeContainer $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new AttributeContainer();
        $this->container->makeAttributeSetAction();
        $this->action = $this->container->getAttributeSetAction();
    }

    /** @test */
    public function initialize_attribute() {
        $attribute = $this->eavFactory->createAttribute();
        $result = $this->action->initializeAttribute($attribute);
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals($attribute->toArray(),  $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }

    /** @test */
    public function initialized_attribute_without_pivot() {
        $domainModel = $this->eavFactory->createDomain();
        $setModel = $this->eavFactory->createAttributeSet($domainModel);
        $groupModel = $this->eavFactory->createGroup($setModel);
        $attributeModel = $this->eavFactory->createAttribute($domainModel);
        $this->eavFactory->createPivot($domainModel, $setModel, $groupModel, $attributeModel);
        $attribute = $setModel->attributes()->get()->first();
        $result = $this->action->initializeAttribute($attribute);
        $this->assertEquals($attributeModel->toArray(), $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }

    /** @test */
    public function initialized_strategy() {
        $attribute = new Attribute();
        $result = $this->action->initializeStrategy($attribute);
        $this->assertInstanceOf(Strategy::class, $result);
        $this->assertSame($result, $this->container->getStrategy());
    }

    /** @test */
    public function initialize() {
        $domainModel = $this->eavFactory->createDomain();
        $entityModel = $this->eavFactory->createEntity($domainModel);
        $setModel = $this->eavFactory->createAttributeSet($domainModel);
        $groupModel = $this->eavFactory->createGroup($setModel);
        $attributeModel = $this->eavFactory->createAttribute($domainModel);
        $this->eavFactory->createPivot($domainModel, $setModel, $groupModel, $attributeModel);
        $valueModel = $this->eavFactory->createValue(
            ATTR_TYPE::STRING, $domainModel, $entityModel, $attributeModel, "test");

        $entity = new Entity();
        $entity->setKey($entityModel->getKey());
        $entity->setDomainKey($domainModel->getKey());
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);

        $this->container
            ->setAttributeSet($attrSet)
            ->makeValueAction();
        $this->action->initialize($attributeModel);

        // attribute
        $attribute = $this->container->getAttribute();
        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals($attributeModel->toArray(), $attribute->getBag()->getFields());
        // value
        $valueManager = $this->container->getValueManager();
        $this->assertEquals($valueModel->getKey(), $valueManager->getKey());
        $this->assertEquals($valueModel->getValue(), $valueManager->getStored());
    }

    /** @test */
    public function initialize_calls() {
        $attributeModel = $this->eavFactory->createAttribute();
        $container = $this->getMockBuilder(AttributeContainer::class)
            ->onlyMethods(['makeValueManager'])
            ->getMock();
        $container->expects($this->once())
            ->method('makeValueManager');
        $action = $this->getMockBuilder(AttributeSetAction::class)
            ->onlyMethods([
                'initializeAttribute',
                'initializeStrategy',
                'getAttributeContainer',
            ])
            ->getMock();
        $action->expects($this->once())
            ->method('getAttributeContainer')
            ->willReturn($container);
        $attribute = new Attribute();
        $action->expects($this->once())
            ->method('initializeAttribute')
            ->with($attributeModel)
            ->willReturn($attribute);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['find'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('find');
        $action->expects($this->once())
            ->method('initializeStrategy')
            ->with($attribute)
            ->willReturn($strategy);
        $action->initialize($attributeModel);
    }

}