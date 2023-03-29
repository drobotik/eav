<?php

namespace Tests\Unit\Attribute;

use Kuperwood\Eav\Attribute;
use Kuperwood\Eav\EavContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Domain;
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Strategy;
use Kuperwood\Eav\ValueManager;

use PHPUnit\Framework\TestCase;

class AttributeContainerTest extends TestCase
{
    private EavContainer $container;

    public function setUp(): void
    {
        parent::setUp();
        $this->container = new EavContainer();
    }

    /** @test */
    public function domain() {
        $domain = new Domain();
        $result = $this->container->setDomain($domain);
        $this->assertSame($result, $this->container);
        $this->assertSame($domain, $this->container->getDomain());
        $this->assertSame($this->container, $this->container->getDomain()->getEavContainer());
    }

    /** @test */
    public function entity() {
        $entity = new Entity();
        $result = $this->container->setEntity($entity);
        $this->assertSame($result, $this->container);
        $this->assertSame($entity, $this->container->getEntity());
        $this->assertSame($this->container, $this->container->getEntity()->getEavContainer());
    }

    /** @test */
    public function attribute_set() {
        $attributeSet = new AttributeSet();
        $result = $this->container->setAttributeSet($attributeSet);
        $this->assertSame($result, $this->container);
        $this->assertSame($attributeSet, $this->container->getAttributeSet());
        $this->assertSame($this->container, $this->container->getAttributeSet()->getEavContainer());
    }

    /** @test */
    public function attribute() {
        $attribute = new Attribute();
        $result = $this->container->setAttribute($attribute);
        $this->assertSame($result, $this->container);
        $this->assertSame($attribute, $this->container->getAttribute());
        $this->assertSame($this->container, $this->container->getAttribute()->getEavContainer());
    }

    /** @test */
    public function strategy() {
        $strategy = new Strategy();
        $result = $this->container->setStrategy($strategy);
        $this->assertSame($result, $this->container);
        $this->assertSame($strategy, $this->container->getStrategy());
        $this->assertSame($this->container, $this->container->getStrategy()->getEavContainer());
    }

    /** @test */
    public function value_manager() {
        $valueManager = new ValueManager();
        $result = $this->container->setValueManager($valueManager);
        $this->assertSame($result, $this->container);
        $this->assertSame($valueManager, $this->container->getValueManager());
        $this->assertSame($this->container, $this->container->getValueManager()->getEavContainer());
    }

    /** @test */
    public function makeDomain() {
        $instance = $this->container->make(Domain::class);
        $this->assertSame($this->container, $instance->getEavContainer());
        $this->assertInstanceOf(Domain::class, $instance);
    }

    /** @test */
    public function makeEntity() {
        $instance = $this->container->make(Entity::class);
        $this->assertSame($this->container, $instance->getEavContainer());
        $this->assertInstanceOf(Entity::class, $instance);
    }

    /** @test */
    public function makeAttributeSet() {
        $instance = $this->container->make(AttributeSet::class);
        $this->assertSame($this->container, $instance->getEavContainer());
        $this->assertInstanceOf(AttributeSet::class, $instance);
    }

    /** @test */
    public function makeAttribute() {
        $instance = $this->container->make(Attribute::class);
        $this->assertSame($this->container, $instance->getEavContainer());
        $this->assertInstanceOf(Attribute::class, $instance);
    }

    /** @test */
    public function makeStrategy() {
        $instance = $this->container->make(Strategy::class);
        $this->assertSame($this->container, $instance->getEavContainer());
        $this->assertInstanceOf(Strategy::class, $instance);
    }

    /** @test */
    public function makeValueManager() {
        $instance = $this->container->make(ValueManager::class);
        $this->assertSame($this->container, $instance->getEavContainer());
        $this->assertInstanceOf(ValueManager::class, $instance);
    }

    /** @test */
    public function makeDomainAlias() {
        $result = $this->container->makeDomain();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(Domain::class, $this->container->getDomain());
    }

    /** @test */
    public function makeEntityAlias() {
        $result = $this->container->makeEntity();
        $this->assertSame($result, $this->container);
        $this->assertInstanceOf(Entity::class, $this->container->getEntity());
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
}