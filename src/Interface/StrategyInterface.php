<?php

namespace Kuperwood\Eav\Interface;

use Kuperwood\Eav\Result\Result;

interface StrategyInterface
{
    public function rules(): ?array;
    public function validateAction(): Result;
    public function findAction() : Result;
    public function saveAction() : Result;
    public function deleteAction() : Result;

}