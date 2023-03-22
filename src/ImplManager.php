<?php

namespace Kuperwood\Eav;

use Kuperwood\Eav\Interface\AttributeGroupInterface;
use Kuperwood\Eav\Interface\AttributeInterface;
use Kuperwood\Eav\Interface\AttributeSetInterface;
use Kuperwood\Eav\Interface\DomainInterface;
use Kuperwood\Eav\Interface\EntityInterface;
use Kuperwood\Eav\Interface\PivotInterface;
use Kuperwood\Eav\Interface\ValueInterface;

class ImplManager
{
    protected DomainInterface $domain;
    protected EntityInterface $entity;
    protected AttributeSetInterface $attributeSet;
    protected AttributeGroupInterface $attributeGroup;
    protected AttributeInterface $attribute;
    protected PivotInterface $pivot;
    protected ValueInterface $value;

    public function getDomain() : DomainInterface
    {
        return $this->domain;
    }

    public function setDomain(DomainInterface $domain)
    {
        $this->domain = $domain;
    }

    public function getEntity() : EntityInterface
    {
        return $this->entity;
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    public function getAttributeSet() : AttributeSetInterface
    {
        return $this->attributeSet;
    }

    public function setAttributeSet(AttributeSetInterface $attributeSet)
    {
        $this->attributeSet = $attributeSet;
    }


    public function getAttributeGroup() : AttributeGroupInterface
    {
        return $this->attributeGroup;
    }

    public function setAttributeGroup(AttributeGroupInterface $attributeGroup)
    {
        $this->attributeGroup = $attributeGroup;
    }

    public function getAttribute() : AttributeInterface
    {
        return $this->attribute;
    }

    public function setAttribute(AttributeInterface $attribute)
    {
        $this->attribute = $attribute;
    }

    public function getPivot() : PivotInterface
    {
        return $this->pivot;
    }

    public function setPivot(PivotInterface $pivot)
    {
        $this->pivot = $pivot;
    }

    public function getValue() : ValueInterface
    {
        return $this->value;
    }

    public function setValue(ValueInterface $value)
    {
        $this->value = $value;
    }

}