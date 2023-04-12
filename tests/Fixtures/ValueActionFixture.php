<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Fixtures;

use Drobotik\Eav\Result\Result;
use Drobotik\Eav\Value\ValueAction;

class ValueActionFixture extends ValueAction
{
    public function create(): Result
    {
        $this->getAttributeContainer()->getStrategy()->lifecycle[] = "createValue";
        return (new Result())->created();
    }

    public function update(): Result
    {
        $this->getAttributeContainer()->getStrategy()->lifecycle[] = "updateValue";
        return (new Result())->updated();
    }

    public function delete(): Result
    {
        $this->getAttributeContainer()->getStrategy()->lifecycle[] = "deleteValue";
        return (new Result())->deleted();
    }
}