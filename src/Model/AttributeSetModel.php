<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Doctrine\DBAL\Exception;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\_PIVOT;
use Drobotik\Eav\Enum\_SET;

class AttributeSetModel extends Model
{
    public function __construct()
    {
        $this->setTable(_SET::table());
        $this->setPrimaryKey(_SET::ID->column());
    }

    public function create(array $data) : int
    {
        $conn = $this->db();
        $conn->createQueryBuilder()
            ->insert($this->getTable())
            ->values([
                _SET::DOMAIN_ID->column() => '?',
                _SET::NAME->column() => '?'
            ])
            ->setParameter(0, $data[_SET::DOMAIN_ID->column()])
            ->setParameter(1, $data[_SET::NAME->column()])
            ->executeQuery();
        return (int) $conn->lastInsertId();
    }

    /**
     * @throws Exception
     */
    public function findAttributes(int $domainKey, int $setKey = null) : array
    {
        $query = $this->db()
            ->createQueryBuilder()
            ->select('a.*')
            ->from(_ATTR::table(), 'a')
            ->innerJoin('a', _PIVOT::table(), 'p',
                sprintf('a.%s = p.%s', _ATTR::ID->column(), _PIVOT::ATTR_ID->column())
            )
            ->where(sprintf('p.%s = ?', _PIVOT::DOMAIN_ID->column()))
            ->setParameter(0, $domainKey);

        if(!is_null($setKey))
            $query = $query
                ->andWhere(sprintf('p.%s = ?', _PIVOT::SET_ID->column()))
                ->setParameter(1, $setKey);

        return $query
            ->executeQuery()
            ->fetchAllAssociative();
    }
}