<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Traits\ContainerTrait;

class Attribute
{
    use ContainerTrait;

    private AttributeBag      $bag;

    public function __construct()
    {
        $this->setBag(new AttributeBag());
    }

    public function getBag(): AttributeBag
    {
        return $this->bag;
    }

    public function setBag(AttributeBag $bag): self
    {
        $this->bag = $bag;

        return $this;
    }

    public function getKey()
    {
        return $this->getBag()->getField(_ATTR::ID);
    }

    public function setKey($key): self
    {
        $this->getBag()->setField(_ATTR::ID, $key);

        return $this;
    }

    public function getDomainKey(): ?int
    {
        return $this->getBag()->getField(_ATTR::DOMAIN_ID);
    }

    public function setDomainKey(?int $key): self
    {
        $this->getBag()->setField(_ATTR::DOMAIN_ID, $key);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->getBag()->getField(_ATTR::NAME);
    }

    public function setName(?string $name): self
    {
        $this->getBag()->setField(_ATTR::NAME, $name);

        return $this;
    }

    /**
     * @throws AttributeException
     */
    public function getType(): string
    {
        $type = $this->getBag()->getField(_ATTR::TYPE);
        return ATTR_TYPE::getCase($type);
    }

    public function setType(string $type): self
    {
        $this->getBag()->setField(_ATTR::TYPE, $type);

        return $this;
    }

    public function getStrategy(): string
    {
        return $this->getBag()->getField(_ATTR::STRATEGY);
    }

    public function setStrategy(string $strategy): self
    {
        $this->getBag()->setField(_ATTR::STRATEGY, $strategy);

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->getBag()->getField(_ATTR::SOURCE);
    }

    public function setSource(?string $source): self
    {
        $this->getBag()->setField(_ATTR::SOURCE, $source);

        return $this;
    }

    public function getDefaultValue(): ?string
    {
        return $this->getBag()->getField(_ATTR::DEFAULT_VALUE);
    }

    public function setDefaultValue($value): self
    {
        $this->getBag()->setField(_ATTR::DEFAULT_VALUE, $value);

        return $this;
    }

    public function getDescription(): string
    {
        return $this->getBag()->getField(_ATTR::DESCRIPTION);
    }

    public function setDescription($value): self
    {
        $this->getBag()->setField(_ATTR::DESCRIPTION, $value);

        return $this;
    }
}
