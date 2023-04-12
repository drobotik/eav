<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Illuminate\Database\Eloquent\Model;
use Drobotik\Eav\Enum\_DOMAIN;

class DomainModel extends Model
{
    public function __construct(array $attributes = [])
    {
        $this->table = _DOMAIN::table();
        $this->primaryKey = _DOMAIN::ID->column();
        $this->fillable = [
            _DOMAIN::NAME->column()
        ];
        $this->timestamps = false;
        parent::__construct($attributes);
    }

    public function getName()
    {
        return $this->{_DOMAIN::NAME->column()};
    }

    public function setName($name) : self
    {
        $this->{_DOMAIN::NAME->column()} = $name;
        return $this;
    }
}