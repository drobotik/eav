<?php

declare(strict_types=1);

namespace Tests\Eav\AttributeSetAction;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\AttributeSetAction;
use Drobotik\Eav\Entity;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Strategy;
use Tests\TestCase;

class AttributeSetActionFunctionalTest extends TestCase
{
    private AttributeContainer $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new AttributeContainer();
        $this->container->makeAttributeSetAction();
        $this->action = $this->container->getAttributeSetAction();
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSetAction::initializeAttribute
     */
    public function initialize_attribute() {
        $attribute = $this->eavFactory->createAttribute();
        $result = $this->action->initializeAttribute($attribute);
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals($attribute->toArray(),  $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSetAction::initializeAttribute
     */
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
    /**
     * @test
     * @group functional
     * @covers AttributeSetAction::initializeStrategy
     */
    public function initialized_strategy() {
        $attribute = new Attribute();
        $result = $this->action->initializeStrategy($attribute);
        $this->assertInstanceOf(Strategy::class, $result);
        $this->assertSame($result, $this->container->getStrategy());
    }
    /**
     * @test
     * @group functional
     * @covers AttributeSetAction::initialize
     */
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

        $this->container->setAttributeSet($attrSet);
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
}