<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Exception;

class EntityBag extends Transporter
{
    private Entity $entity;

    public function setEntity(Entity $entity) : self
    {
        $this->entity = $entity;
        return $this;
    }

    public function getEntity() : Entity
    {
        return $this->entity;
    }

    public function setField($field, $value): Transporter
    {
        $set =  $this->getEntity()->getAttributeSet();
        if($set->hasContainer($field)) {
            $container = $set->getContainer($field);
            $valueManager = $container->getValueManager();
            $valueManager->setValue($value);
        }
        return parent::setField($field, $value);
    }

    public function removeField(string $field): void
    {
        $set = $this->getEntity()->getAttributeSet();
        if($set->hasContainer($field)) {
            $set->getContainer($field)
                ->getValueManager()
                ->clearRuntime();
        }
        parent::removeField($field);
    }

    public function setFields(array $data) : self
    {
        foreach($data as $field => $value) {
            $this->setField($field, $value);
        }
        return $this;
    }
}