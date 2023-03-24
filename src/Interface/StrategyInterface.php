<?php

namespace Kuperwood\Eav\Interface;

use Kuperwood\Eav\Result\ValueResult;

interface StrategyInterface
{
    public function rules(): array;
    public function validate(): false|array;
    public function find() : ValueResult;
    public function save(string $type) : ValueResult;
    public function destroy() : ValueResult;

}