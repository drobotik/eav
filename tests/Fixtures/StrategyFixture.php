<?php

namespace Tests\Fixtures;

use Kuperwood\Eav\Strategy;

class StrategyFixture extends Strategy
{
    public array $creatingLifecycle = [];
    public array $updatingLifecycle = [];
    public array $deletingLifecycle = [];

    public function beforeCreate(): void
    {
        $this->creatingLifecycle[] = 'before';
    }

    public function afterCreate(): void
    {
        $this->creatingLifecycle[] = 'after';
    }

    public function beforeUpdate(): void
    {
        $this->updatingLifecycle[] = 'before';
    }

    public function afterUpdate(): void
    {
        $this->updatingLifecycle[] = 'after';
    }

    public function beforeDelete(): void
    {
        $this->deletingLifecycle [] = 'before';
    }

    public function afterDelete(): void
    {
        $this->deletingLifecycle [] = 'after';
    }
}