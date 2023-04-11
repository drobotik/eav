<?php

namespace Drobotik\Eav\Trait;

use Drobotik\Eav\AttributeContainer;
use Drobotik\Eav\AttributeSet;
use Drobotik\Eav\Model\AttributeSetModel;
use Drobotik\Eav\Model\DomainModel;
use Drobotik\Eav\Model\EntityModel;

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

    public function makeDomainModel() : DomainModel
    {
        return new DomainModel();
    }

}