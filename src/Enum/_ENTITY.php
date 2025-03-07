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
