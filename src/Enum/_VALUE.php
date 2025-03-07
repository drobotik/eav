<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Enum;

use Drobotik\Eav\Interface\DefineTableInterface;

enum _VALUE implements DefineTableInterface
{

    public const ID = 'value_id';
    public const DOMAIN_ID = 'domain_id';
    public const ENTITY_ID = 'entity_id';
    public const ATTRIBUTE_ID = 'attribute_id';
    public const VALUE = 'value';

    public static function table() : string
    {
        return 'eav_value_%s';
    }

    public function column() : string
    {
        return '';
    }


}
