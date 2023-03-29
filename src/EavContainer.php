<?php

namespace Kuperwood\Eav;

class EavContainer
{
    protected Attribute $attribute;
    protected AttributeSet $attributeSet;
    protected Entity $entity;
    protected ValueManager $valueManager;
    protected Strategy $strategy;
    protected Domain $domain;

    public function make(string $className) {
        $supported = [
            Attribute::class,
            AttributeSet::class,
            Entity::class,
            ValueManager::class,
            Strategy::class,
            Domain::class,
        ];

        if(!in_array($className, $supported)) {
            return false;
        }

        $instance = new $className();
        $instance->setEavContainer($this);
        return $instance;
    }

    public function makeDomain() : self
    {
        $this->setDomain($this->make(Domain::class));
        return $this;
    }

    public function makeEntity() : self
    {
        $this->setEntity($this->make(Entity::class));
        return $this;
    }

    public function makeAttributeSet() : self
    {
        $this->setAttributeSet($this->make(AttributeSet::class));
        return $this;
    }

    public function makeAttribute() : self
    {
        $this->setAttribute($this->make(Attribute::class));
        return $this;
    }

    public function makeStrategy() : self
    {
        $this->setStrategy($this->make(Strategy::class));
        return $this;
    }

    public function makeValueManager() : self
    {
        $this->setValueManager($this->make(ValueManager::class));
        return $this;
    }

    public function setDomain(Domain $domain) : self
    {
        $domain->setEavContainer($this);
        $this->domain = $domain;
        return $this;
    }

    public function getDomain(): Domain
    {
        return $this->domain;
    }

    public function setAttribute(Attribute $attribute) : self
    {
        $attribute->setEavContainer($this);
        $this->attribute = $attribute;
        return $this;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function setAttributeSet(AttributeSet $attributeSet) : self
    {
        $attributeSet->setEavContainer($this);
        $this->attributeSet = $attributeSet;
        return $this;
    }

    public function getAttributeSet(): AttributeSet
    {
        return $this->attributeSet;
    }

    public function setEntity(Entity $entity) : self
    {
        $entity->setEavContainer($this);
        $this->entity = $entity;
        return $this;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    public function setValueManager(ValueManager $valueManager) : self
    {
        $valueManager->setEavContainer($this);
        $this->valueManager = $valueManager;
        return $this;
    }

    public function getValueManager(): ValueManager
    {
        return $this->valueManager;
    }

    public function setStrategy(Strategy $strategy) : self
    {
        $strategy->setEavContainer($this);
        $this->strategy = $strategy;
        return $this;
    }

    public function getStrategy(): Strategy
    {
        return $this->strategy;
    }
}