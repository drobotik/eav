<?php
/**
 * This file is part of the eav package.
 *
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Value;

use Kuperwood\Eav\Traits\ContainerTrait;

class ValueManager
{
    use ContainerTrait;
    private ValueState $runtime;
    private ValueState $stored;

    private $key;

    public function __construct()
    {
        $this->runtime = new ValueState();
        $this->stored = new ValueState();
    }

    public function hasKey(): bool
    {
        return isset($this->key) && $this->key > 0;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key): self
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

    public function getStored()
    {
        return $this->stored->get();
    }

    public function setStored($value): self
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

    public function getRuntime()
    {
        return $this->runtime->get();
    }

    public function setRuntime($value): self
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

    public function setValue($value): self
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
