<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Interface;

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