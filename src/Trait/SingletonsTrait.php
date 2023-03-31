<?php

namespace Kuperwood\Eav\Trait;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\Model\AttributeSetModel;

trait SingletonsTrait
{
    public function makeAttributeSetModel() : AttributeSetModel
    {
        return new AttributeSetModel;
    }

    public function makeAttributeContainer() : AttributeContainer
    {
        return new AttributeContainer;
    }

}