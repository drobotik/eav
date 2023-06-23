<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Eav\AttributeSetAction;

use Drobotik\Eav\Attribute;
use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
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
     * @covers \Drobotik\Eav\AttributeSetAction::initializeAttribute
     */
    public function initialize_attribute() {
        $attribute = $this->eavFactory->createAttribute();
        $result = $this->action->initializeAttribute($attribute->toArray());
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals($attribute->toArray(),  $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeSetAction::initializeAttribute
     */
    public function initialized_attribute_without_pivot() {
        $domainKey = $this->eavFactory->createDomain();
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupModel = $this->eavFactory->createGroup($setKey);
        $attributeModel = $this->eavFactory->createAttribute($domainKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupModel->getKey(), $attributeModel->getKey());
        $result = $this->action->initializeAttribute($attributeModel->toArray());
        $this->assertEquals($attributeModel->toArray(), $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }
    /**
     * @test
     * @group functional
     * @covers \Drobotik\Eav\AttributeSetAction::initializeStrategy
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
     * @covers \Drobotik\Eav\AttributeSetAction::initialize
     */
    public function initialize() {
        $domainKey = $this->eavFactory->createDomain();
        $entityKey = $this->eavFactory->createEntity($domainKey);
        $setKey = $this->eavFactory->createAttributeSet($domainKey);
        $groupModel = $this->eavFactory->createGroup($setKey);
        $attributeModel = $this->eavFactory->createAttribute($domainKey);
        $this->eavFactory->createPivot($domainKey, $setKey, $groupModel->getKey(), $attributeModel->getKey());
        $valueModel = $this->eavFactory->createValue(
            ATTR_TYPE::STRING, $domainKey, $entityKey, $attributeModel->getKey(), "test");

        $entity = new Entity();
        $entity->setKey($entityKey);
        $entity->setDomainKey($domainKey);
        $attrSet = new AttributeSet();
        $attrSet->setEntity($entity);

        $this->container->setAttributeSet($attrSet);
        $this->action->initialize($attributeModel->toArray());

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