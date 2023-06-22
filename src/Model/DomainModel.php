<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Drobotik\Eav\Enum\_DOMAIN;

class DomainModel extends Model
{
    private string $name;

    public function __construct()
    {
        $this->setTable(_DOMAIN::table());
        $this->setKeyName(_DOMAIN::ID->column());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function create() : int
    {
        return $this->insert([
            _DOMAIN::NAME->column() => $this->getName()
        ]);
    }

    public function toArray(): array
    {
        $result = parent::toArray();
        if(isset($this->name))
            $result[_DOMAIN::NAME->column()] = $this->getName();
        return $result;
    }


}