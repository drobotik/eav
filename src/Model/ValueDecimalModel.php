<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Trait\SingletonsTrait;

class ValueDecimalModel extends ValueBase
{
    use SingletonsTrait;

    public function __construct(array $attributes = [])
    {
        $this->table = ATTR_TYPE::DECIMAL->valueTable();
        parent::__construct($attributes);
    }

    public function setValue($value) : self
    {
        $parser = $this->makeValueParser();
        $this->{_VALUE::VALUE->column()} = $parser->parseDecimal($value);
        return $this;
    }
}