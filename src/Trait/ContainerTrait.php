<?php

namespace Kuperwood\Eav\Trait;

use Kuperwood\Eav\AttributeContainer;

trait ContainerTrait
{
    protected AttributeContainer $attributeContainer;

    public function setAttributeContainer(AttributeContainer $attributeContainer)
    {
        $this->attributeContainer = $attributeContainer;
    }

    public function getAttributeContainer(): AttributeContainer
    {
        return $this->attributeContainer;
    }
}