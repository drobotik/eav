<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Value;

use Drobotik\Eav\DependencyManager;
use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Trait\ContainerTrait;
use Illuminate\Validation\Validator;

class ValueValidator
{
    use ContainerTrait;

    public function getDefaultValueRule(): array
    {
        return $this->getAttributeContainer()
            ->getAttribute()
            ->getType()
            ->validationRule()
        ;
    }

    public function getRules(): array
    {
        $rules = $this->getAttributeContainer()->getStrategy()->rules();

        return [
            _VALUE::ENTITY_ID->column() => ['required', 'integer'],
            _VALUE::DOMAIN_ID->column() => ['required', 'integer'],
            _VALUE::ATTRIBUTE_ID->column() => ['required', 'integer'],
            _VALUE::VALUE->column() => is_null($rules)
                ? $this->getDefaultValueRule()
                : $rules,
        ];
    }

    public function getValidatedData(): array
    {
        $container = $this->getAttributeContainer();
        $attribute = $container->getAttribute();
        $entity = $container->getAttributeSet()->getEntity();
        $valueManager = $container->getValueManager();

        return [
            _VALUE::ENTITY_ID->column() => $entity->getKey(),
            _VALUE::DOMAIN_ID->column() => $entity->getDomainKey(),
            _VALUE::ATTRIBUTE_ID->column() => $attribute->getKey(),
            _VALUE::VALUE->column() => $valueManager->getRuntime(),
        ];
    }

    public function getValidator(): Validator
    {
        return DependencyManager::getContainer()->getValidator()->make(
            $this->getValidatedData(),
            $this->getRules()
        );
    }

    public function validateField(): null|array
    {
        $output = null;
        $container = $this->getAttributeContainer();
        $strategy = $container->getStrategy();
        $result = $strategy->validate();
        if ($result->getCode() == _RESULT::VALIDATION_FAILS->code()) {
            $output = $result->getData();
        }

        return $output;
    }
}
