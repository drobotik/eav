<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Repository;

use Drobotik\Eav\Enum\_GROUP;
use Drobotik\Eav\Exception\EntityFactoryException;
use Drobotik\Eav\Model\AttributeGroupModel;

class GroupRepository extends BaseRepository
{
    public function checkIsBelongsTo(int $setKey, array $keys)
    {
        $groupKeys = AttributeGroupModel::query()
            ->where(_GROUP::SET_ID->column(), $setKey)
            ->pluck(_GROUP::NAME->column(), _GROUP::ID->column());

        $groupDiff = array_diff_key($keys, $groupKeys->toArray());
        if (count($groupDiff) > 0) {
            throw new EntityFactoryException('Groups not found');
        }
    }
}