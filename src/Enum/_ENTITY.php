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

class _ENTITY implements DefineTableInterface
{
    public const ID = 'entity_id';
    public const DOMAIN_ID = 'domain_id';
    public const ATTR_SET_ID = 'set_id';
    public const SERVICE_KEY = 'service_key';

    public static function table() : string
    {
        return 'eav_entities';
    }

}
