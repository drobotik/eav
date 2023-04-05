<?php

namespace Kuperwood\Eav;

use Kuperwood\Eav\Enum\_RESULT;
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

    public function validateField() : null|array
    {
        $output = null;
        $container = $this->getAttributeContainer();
        $strategy = $container->getStrategy();
        $result = $strategy->validate();
        if($result->getCode() == _RESULT::VALIDATION_FAILS->code())
            $output = $result->getData();
        return $output;
    }
}