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
use Kuperwood\Eav\Strategy;

class StrategyFixture extends Strategy
{
    public array $lifecycle = [];

    public function beforeCreate(): void
    {
        $this->lifecycle[] = 'beforeCreate';
    }

    public function createValue(): Result
    {
        $this->lifecycle[] = 'createValue';
        return (new Result())->created();
    }

    public function afterCreate(): void
    {
        $this->lifecycle[] = 'afterCreate';
    }

    public function updateValue(): Result
    {
        $this->lifecycle[] = 'updateValue';
        return (new Result())->updated();
    }

    public function beforeUpdate(): void
    {
        $this->lifecycle[] = 'beforeUpdate';
    }

    public function afterUpdate(): void
    {
        $this->lifecycle[] = 'afterUpdate';
    }

    public function beforeDelete(): void
    {
        $this->lifecycle [] = 'beforeDelete';
    }

    public function afterDelete(): void
    {
        $this->lifecycle [] = 'afterDelete';
    }

    public function deleteValue(): Result
    {
        $this->lifecycle[] = 'deleteValue';
        return (new Result())->deleted();
    }
}