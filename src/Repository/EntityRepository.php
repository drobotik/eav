<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Repository;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Model\EntityModel;

class EntityRepository extends BaseRepository
{

    public function generateServiceKey(): int
    {
        return mt_rand(1000000, 999999999);
    }

    public function getServiceKey() : int
    {
        $key = $this->generateServiceKey();
        return $this->isServiceKey($key)
            ? $this->getServiceKey()
            : $key;
    }

    public function isServiceKey(int $key) : bool
    {
        return EntityModel::query()
            ->where(_ENTITY::SERVICE_KEY->column(), '=', $key)
            ->exists();
    }

    public function getByServiceKey(int $key)
    {
        return EntityModel::query()
            ->where(_ENTITY::SERVICE_KEY->column(), $key)
            ->get()
            ->keyBy(_ENTITY::ID->column());
    }

    public function bulkCreate(int $amount, int $domainKey, int $setKey, int $serviceKey) : void
    {
        $format = "($domainKey, $setKey, $serviceKey)";
        $bulk = [];
        for($i=0;$i<$amount;$i++) {
            $bulk[] = $format;
        }
        $template = sprintf(
            "INSERT INTO "._ENTITY::table()." ("._ENTITY::DOMAIN_ID->column().", "._ENTITY::ATTR_SET_ID->column().", "._ENTITY::SERVICE_KEY->column().") VALUES %s;",
            implode(',',$bulk)
        );

        Connection::pdo()->exec($template);
    }

}