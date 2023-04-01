<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\Value\ValueValidator;
use Kuperwood\Eav\ValueManager;
use Tests\TestCase;

class AttributeContainerTest extends TestCase
{
    private AttributeContainer $container;


    public function setUp(): void
    {
        parent::setUp();
        $this->container = new AttributeContainer();
    }

    /** @test */
    public function attribute_set() {
        $attributeSet = new AttributeSet();
        $result = $this->container->setAttributeSet($attributeSet);
        $this->assertSame($result, $this->container);
        $this->assertSame($attributeSet , $this->container->getAttributeSet());
        $this->assertSame($this->container, $this->container->getAttributeSet()->getAttributeContainer());
    }

    /** @test */
    public function attribute() {
        $attribute = new Attribute();
        $result = $this->container->setAttribute($attribute);
        $this->assertSame($result, $this->container);
        $this->assertSame($attribute, $this->container->getAttribute());
        $this->assertSame($this->container, $this->container->getAttribute()->getAttributeContainer());
    }

    /** @test */
    public function strategy() {
        $strategy = new Strategy();
        $result = $this->container->setStrategy($strategy);
        $this->assertSame($result, $this->container);
        $this->assertSame($strategy, $this->container->getStrategy());
        $this->assertSame($this->container, $this->container->getStrategy()->getAttributeContainer());
    }

    /** @test */
    public function value_manager() {
        $valueManager = new ValueManager();
        $result = $this->container->setValueManager($valueManager);
        $this->assertSame($result, $this->container);
        $this->assertSame($valueManager, $this->container->getValueManager());
        $this->assertSame($this->container, $this->container->getValueManager()->getAttributeContainer());
    }

    /** @test */
    public function value_validator() {
        $valueValidator = new ValueValidator();
        $result = $this->container->setValueValidator($valueValidator);
        $this->assertSame($result, $this->container);
        $this->assertSame($valueValidator, $this->container->getValueValidator());
        $this->assertSame($this->container, $this->container->getValueValidator()->getAttributeContainer());
    }

    /** @test */
    public function makeAttributeSet() {
        $instance = $this->container->make(AttributeSet::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(AttributeSet::class, $instance);
    }

    /** @test */
    public function makeAttribute() {
        $instance = $this->container->make(Attribute::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(Attribute::class, $instance);
    }

    /** @test */
    public function makeStrategy() {
        $instance = $this->container->make(Strategy::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(Strategy::class, $instance);
    }

    /** @test */
    public function makeValueManager() {
        $instance = $this->container->make(ValueManager::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(ValueManager::class, $instance);
    }

    /** @test */
    public function makeValueValidator() {
        $instance = $this->container->make(ValueValidator::class);
        $this->assertSame($this->container, $instance->getAttributeContainer());
        $this->assertInstanceOf(ValueValidator::class, $instance);
    }

    /** @test */
    public function makeAttributeSetAlias() {
        $result = $this->container->makeAttributeSet();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(AttributeSet::class, $this->container->getAttributeSet());
    }

    /** @test */
    public function makeAttributeAlias() {
        $result = $this->container->makeAttribute();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(Attribute::class, $this->container->getAttribute());
    }

    /** @test */
    public function makeStrategyAlias() {
        $result = $this->container->makeStrategy();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(Strategy::class, $this->container->getStrategy());
    }

    /** @test */
    public function makeValueManagerAlias() {
        $result = $this->container->makeValueManager();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(ValueManager::class, $this->container->getValueManager());
    }

    /** @test */
    public function makeValueValidatorAlias() {
        $result = $this->container->makeValueValidator();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(ValueValidator::class, $this->container->getValueValidator());
    }

    /** @test */
    public function initialize_attribute() {
        $attribute = $this->eavFactory->createAttribute();
        $result = $this->container->initializeAttribute($attribute);
        $this->assertInstanceOf(Attribute::class, $result);
        $this->assertEquals($attribute->toArray(),  $result->getBag()->getFields());
    }

    /** @test */
    public function initialized_attribute_without_pivot() {
        $domainModel = $this->eavFactory->createDomain();
        $setModel = $this->eavFactory->createAttributeSet($domainModel);
        $groupModel = $this->eavFactory->createGroup($setModel);
        $attributeModel = $this->eavFactory->createAttribute($domainModel);
        $this->eavFactory->createPivot($domainModel, $setModel, $groupModel, $attributeModel);
        $attribute = $setModel->attributes()->get()->first();
        $result = $this->container->initializeAttribute($attribute);
        $this->assertEquals($attributeModel->toArray(), $result->getBag()->getFields());
        $this->assertSame($result, $this->container->getAttribute());
    }

    /** @test */
    public function initialized_strategy() {
        $attribute = new Attribute();
        $result = $this->container->initializeStrategy($attribute);
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
            ->makeValueAction()
            ->initialize($attributeModel);

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
            ->onlyMethods([
                'initializeAttribute',
                'initializeStrategy',
                'makeValueManager',
            ])
            ->getMock();
        $attribute = new Attribute();
        $container->expects($this->once())
            ->method('initializeAttribute')
            ->with($attributeModel)
            ->willReturn($attribute);
        $strategy = $this->getMockBuilder(Strategy::class)
            ->onlyMethods(['find'])
            ->getMock();
        $strategy->expects($this->once())
            ->method('find');
        $container->expects($this->once())
            ->method('initializeStrategy')
            ->with($attribute)
            ->willReturn($strategy);
        $container->expects($this->once())
            ->method('makeValueManager');
        $container->initialize($attributeModel);
    }

}