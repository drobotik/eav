<?php

namespace Kuperwood\Eav;

use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Trait\ContainerTrait;

class EntityAction
{
    use ContainerTrait;

    public function saveValue($value) : Result
    {
        $container = $this->getAttributeContainer();
        $valueManager = $container->getValueManager();
        $strategy = $container->getStrategy();
        $valueManager->setValue($value);
        return $strategy->save();
    }
}