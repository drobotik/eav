<?php

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