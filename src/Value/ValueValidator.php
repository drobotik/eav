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

use Drobotik\Eav\Enum\_RESULT;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Trait\ContainerTrait;
use Drobotik\Eav\Validation\Constraints\NotBlankConstraint;
use Drobotik\Eav\Validation\Constraints\NumericConstraint;
use Drobotik\Eav\Validation\Constraints\RequiredConstraint;
use Drobotik\Eav\Validation\Validator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValueValidator
{
    use ContainerTrait;

    public function getDefaultValueRule(): array
    {
        return ATTR_TYPE::validationRule($this->getAttributeContainer()->getAttribute()->getType());
    }

    public function getRules(): array
    {
        $rules = $this->getAttributeContainer()->getStrategy()->rules();
        return [
            _VALUE::ENTITY_ID => [new RequiredConstraint(), new NotBlankConstraint(), new NumericConstraint()],
            _VALUE::DOMAIN_ID => [new RequiredConstraint(), new NotBlankConstraint(),  new NumericConstraint()],
            _VALUE::ATTRIBUTE_ID => [new RequiredConstraint(), new NotBlankConstraint(),  new NumericConstraint()],
            _VALUE::VALUE => is_null($rules)
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
            _VALUE::ENTITY_ID => $entity->getKey(),
            _VALUE::DOMAIN_ID => $entity->getDomainKey(),
            _VALUE::ATTRIBUTE_ID => $attribute->getKey(),
            _VALUE::VALUE => $valueManager->getRuntime(),
        ];
    }

    public function getValidator(): Validator
    {
        return new Validator();
    }

    public function validateField(): null|array
    {
        $output = null;
        $container = $this->getAttributeContainer();
        $strategy = $container->getStrategy();
        $result = $strategy->validate();
        if ($result->getCode() == _RESULT::VALIDATION_FAILS) {
            $output = $result->getData();
        }

        return $output;
    }
}
