<?php
/**
 * This file is part of the eav package.
 *
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Value;

use Kuperwood\Eav\Enum\_RESULT;
use Kuperwood\Eav\Enum\_VALUE;
use Kuperwood\Eav\Enum\ATTR_TYPE;
use Kuperwood\Eav\Traits\ContainerTrait;
use Kuperwood\Eav\Validation\Constraints\NotBlankConstraint;
use Kuperwood\Eav\Validation\Constraints\NumericConstraint;
use Kuperwood\Eav\Validation\Constraints\RequiredConstraint;
use Kuperwood\Eav\Validation\Validator;

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

    public function validateField()
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
