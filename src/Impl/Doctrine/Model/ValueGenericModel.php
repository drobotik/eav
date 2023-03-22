<?php

namespace Kuperwood\Eav\Impl\Doctrine\Model;

class ValueGenericModel
{
    private ?int $value_id = null;
    private ?int $domain_id;
    private ?int $entity_id;
    private ?int $attribute_id;

    public function getId() : ?int
    {
        return $this->value_id;
    }

    public function getDomainId(): ?int
    {
        return $this->domain_id;
    }

    public function setDomainId(int $id): self
    {
        $this->domain_id = $id;
        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entity_id;
    }

    public function setEntityId(int $id): self
    {
        $this->entity_id = $id;
        return $this;
    }

    public function getAttributeId(): ?int
    {
        return $this->attribute_id;
    }

    public function setAttributeId(int $id): self
    {
        $this->attribute_id = $id;
        return $this;
    }
}