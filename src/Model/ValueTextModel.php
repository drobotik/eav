<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Drobotik\Eav\Enum\ATTR_TYPE;

class ValueTextModel extends ValueBase
{
    public function __construct()
    {
        parent::__construct();
        $this->setTable(ATTR_TYPE::TEXT->valueTable());
    }
}