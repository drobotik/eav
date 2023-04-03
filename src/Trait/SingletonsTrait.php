<?php

namespace Kuperwood\Eav\Trait;

use Kuperwood\Eav\AttributeContainer;
use Kuperwood\Eav\AttributeSet;
use Kuperwood\Eav\Model\AttributeSetModel;
use Kuperwood\Eav\Model\EntityModel;

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

    public function makeEntityModel() : EntityModel
    {
        return new EntityModel();
    }

    public function makeAttributeSet() : AttributeSet
    {
        return new AttributeSet();
    }

}