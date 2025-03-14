<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Traits;

use Drobotik\Eav\AttributeContainer;

trait ContainerTrait
{
    protected AttributeContainer $attributeContainer;

    public function setAttributeContainer(AttributeContainer $attributeContainer): void
    {
        $this->attributeContainer = $attributeContainer;
    }

    public function getAttributeContainer(): AttributeContainer
    {
        return $this->attributeContainer;
    }
}