<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Traits;

use Kuperwood\Eav\AttributeContainer;

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