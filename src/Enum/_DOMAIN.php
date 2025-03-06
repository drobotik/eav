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

enum _DOMAIN implements DefineTableInterface
{
    public const ID = 'domain_id';
    public const NAME = 'name';

    public static function table() : string
    {
        return 'eav_domains';
    }

    public function column() : string
    {
        return '';
    }
}
