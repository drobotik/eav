<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Import;

use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Trait\DomainTrait;
use Drobotik\Eav\Trait\ImportContainerTrait;

class ImportManager
{
    use DomainTrait;
    use ImportContainerTrait;

    public function run(): Result
    {
        $result = new Result();
        $container = $this->getContainer();
        $attributesWorker = $container->getAttributesWorker();
        $attributesWorker->run();
        $contentWorker = $container->getContentWorker();
        $contentWorker->run();
        $result->importSuccess();
        return $result;
    }
}