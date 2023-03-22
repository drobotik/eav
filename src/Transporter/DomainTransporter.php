<?php

namespace Kuperwood\Eav\Transporter;

use Kuperwood\Eav\Enum\_DOMAIN;

class DomainTransporter extends Transporter
{

    public function getKey() : ?string
    {
        return $this->getField(_DOMAIN::ID->column());
    }

    public function setKey(int $key) : self
    {
        $this->setField(_DOMAIN::ID->column(), $key);
        return $this;
    }

    public function getName() : ?string
    {
        return $this->getField(_DOMAIN::NAME->column());
    }

    public function setName(string $name) : self
    {
        $this->setField(_DOMAIN::NAME->column(), $name);
        return $this;
    }
}