<?php

namespace Kuperwood\Eav\Trait;

use Kuperwood\Eav\EavContainer;

trait EavContainerTrait
{
    protected EavContainer $eavContainer;

    public function setEavContainer(EavContainer $attributeContainer)
    {
        $this->eavContainer = $attributeContainer;
    }

    public function getEavContainer(): EavContainer
    {
        return $this->eavContainer;
    }
}