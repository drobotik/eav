<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Trait;

use Drobotik\Eav\Import\ImportContainer;

trait ImportContainerTrait
{
    private ImportContainer $container;

    public function setContainer(ImportContainer $container): void
    {
        $this->container = $container;
    }

    public function getContainer() : ImportContainer
    {
        return $this->container;
    }
}