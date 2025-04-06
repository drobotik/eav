<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests\Fixtures;

use Kuperwood\Eav\Result\Result;
use Kuperwood\Eav\Value\ValueAction;

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