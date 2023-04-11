<?php

namespace Drobotik\Eav\Interface;

use Drobotik\Eav\Result\Result;

interface StrategyInterface
{
    public function validate(): Result;
    public function find() : Result;
    public function save() : Result;
    public function delete() : Result;

}