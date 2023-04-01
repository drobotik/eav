<?php

namespace Kuperwood\Eav\Interface;

interface EavStrategyInterface
{
    public function rules(): ?array;
    public function beforeCreate(): void;
    public function afterCreate(): void;
    public function beforeUpdate(): void;
    public function afterUpdate(): void;
    public function beforeDelete(): void;
    public function afterDelete(): void;
}