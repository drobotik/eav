<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Repository;

use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Model\PivotModel;

class PivotRepository extends BaseRepository
{
    public function createIfNotExist(int $domainKey, int $setKey, int $groupKey, int $attributeKey): PivotModel
    {
        $pivot = PivotModel::query()
            ->where(_PIVOT::DOMAIN_ID->column(), $domainKey)
            ->where(_PIVOT::SET_ID->column(), $setKey)
            ->where(_PIVOT::GROUP_ID->column(), $groupKey)
            ->where(_PIVOT::ATTR_ID->column(), $attributeKey)
            ->first();

        if (is_null($pivot)) {
            $pivot = $this->getFactory()->createPivot($domainKey, $setKey, $groupKey, $attributeKey);
        }

        return $pivot;
    }
}