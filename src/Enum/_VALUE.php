<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Enum;

use Kuperwood\Eav\Interfaces\DefineTableInterface;

class _VALUE implements DefineTableInterface
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

}
