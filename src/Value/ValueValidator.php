<?php

namespace Kuperwood\Eav\Value;

use Illuminate\Validation\Validator;
use Kuperwood\Eav\Container;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Trait\ContainerTrait;

class ValueValidator
{
    use ContainerTrait;
    public function getDefaultValueRule() {
        return $this->getAttributeContainer()
            ->getAttribute()
            ->getType()
            ->validationRule();
    }

    public function getRules() {
        $rules = $this->getAttributeContainer()->getStrategy()->rules();
        return [
            _VALUE::ENTITY_ID->column() => ['required', 'integer'],
            _VALUE::DOMAIN_ID->column() => ['required','integer'],
            _VALUE::ATTRIBUTE_ID->column() => ['required','integer'],
            _VALUE::VALUE->column() => is_null($rules)
                ? $this->getDefaultValueRule()
                : $rules
        ];
    }

    public function getValidatedData() : array
    {
        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $entity = $container->getAttributeSet()->getEntity();
        $valueManager = $container->getValueManager();
        return [
            _VALUE::ENTITY_ID->column() => $entity->getKey(),
            _VALUE::DOMAIN_ID->column() => $entity->getDomainKey(),
            _VALUE::ATTRIBUTE_ID->column() => $attribute->getKey(),
            _VALUE::VALUE->column() => $valueManager->getRuntime()
        ];
    }

    public function getValidator() : Validator
    {
        return Container::getInstance()->getValidator()->make(
            $this->getValidatedData(),
            $this->getRules()
        );
    }
}