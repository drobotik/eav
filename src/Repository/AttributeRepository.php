<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Repository;

use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Exception\AttributeException;
use Drobotik\Eav\Model\AttributeModel;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;

class AttributeRepository extends BaseRepository
{
    public function updateOrCreate(array $data, int $domainKey) : AttributeModel
    {
        if (!key_exists(_ATTR::NAME->column(), $data)) {
            AttributeException::undefinedAttributeName();
        }
        if (!key_exists(_ATTR::TYPE->column(), $data)) {
            AttributeException::undefinedAttributeType();
        }
        // check type is supported
        ATTR_TYPE::getCase($data[_ATTR::TYPE->column()]);

        $name = $data[_ATTR::NAME->column()];
        $attribute = AttributeModel::query()
            ->where(_ATTR::NAME->column(), $name)
            ->where(_ATTR::DOMAIN_ID->column(), $domainKey)
            ->first();

        if (is_null($attribute)) {
            $attribute = $this->getFactory()->createAttribute($domainKey, $data);
        }

        return $attribute;
    }

    public function getStored(int $domainKey, int $setKey = null): Collection
    {
        $query = AttributeModel::query()
            ->where(_ATTR::DOMAIN_ID->column(), $domainKey);

        if (!is_null($setKey))
            $query->with(['pivot' => fn(Builder $q) => $q->where(_PIVOT::SET_ID->column(), $setKey)]);

        return $query->get();
    }

    public function getLinked(int $domainKey, int $setKey): Collection
    {
        $pt = _PIVOT::table();
        $at = _ATTR::table();

        return AttributeModel::query()
            ->join($pt, $pt.'.'._PIVOT::ATTR_ID->column(), '=', $at.'.'._ATTR::ID->column())
            ->where($pt. '.'._PIVOT::SET_ID->column(), '=', $setKey)
            ->where($pt. '.'._PIVOT::DOMAIN_ID->column(), '=', $domainKey)
            ->get();
    }
}