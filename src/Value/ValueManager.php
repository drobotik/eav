<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Value;

use Drobotik\Eav\Trait\ContainerTrait;

class ValueManager
{
    use ContainerTrait;
    private ValueState $runtime;
    private ValueState $stored;

    private int $key;

    public function __construct()
    {
        $this->runtime = new ValueState();
        $this->stored = new ValueState();
    }

    public function hasKey(): bool
    {
        return isset($this->key) && $this->key > 0;
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function setKey(int $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function isClean(): bool
    {
        return $this->isEquivalent();
    }

    private function isEquivalent(): bool
    {
        $orig = $this->getStored();
        $runtime = $this->getRuntime();
        if ($orig === $runtime) {
            return true;
        }
        if (!$this->isRuntime()) {
            return true;
        }

        return is_numeric($orig) && is_numeric($runtime)
            && 0 === strcmp((string) $orig, (string) $runtime);
    }

    public function isStored(): bool
    {
        return $this->stored->isChanged();
    }

    public function getStored(): mixed
    {
        return $this->stored->get();
    }

    public function setStored(mixed $value): self
    {
        $this->stored->set($value);

        return $this;
    }

    public function clearStored(): self
    {
        $this->stored->clear();

        return $this;
    }

    public function isRuntime(): bool
    {
        return $this->runtime->isChanged();
    }

    public function getRuntime(): mixed
    {
        return $this->runtime->get();
    }

    public function setRuntime(mixed $value): self
    {
        $this->runtime->set($value);

        return $this;
    }

    public function clearRuntime(): self
    {
        $this->runtime->clear();

        return $this;
    }

    public function getValue()
    {
        if ($this->isRuntime()) {
            return $this->getRuntime();
        }

        return $this->getStored();
    }

    public function setValue(mixed $value): self
    {
        $this->setRuntime($value);

        return $this;
    }

    public function clearValue(): self
    {
        $this->clearRuntime();

        return $this;
    }
}
