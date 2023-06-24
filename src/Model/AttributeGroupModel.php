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
use Drobotik\Eav\Enum\_GROUP;
use PDO;

class AttributeGroupModel extends Model
{
    public function __construct()
    {
        $this->setTable(_GROUP::table());
        $this->setPrimaryKey(_GROUP::ID->column());
    }

    /**
     * @throws Exception
     */
    public function create(array $data) : int
    {
        $conn = $this->db();
        $conn->createQueryBuilder()
            ->insert(_GROUP::table())
            ->values([
                _GROUP::SET_ID->column() => '?',
                _GROUP::NAME->column() => '?'
            ])
            ->setParameter(0, $data[_GROUP::SET_ID->column()])
            ->setParameter(1, $data[_GROUP::NAME->column()])
            ->executeQuery();
        return (int) $conn->lastInsertId();
    }

    public function checkGroupInAttributeSet(int $setKey, int $groupKey) : bool
    {
        $result = $this->db()->createQueryBuilder()
            ->select($this->getPrimaryKey())
            ->from($this->getTable())
            ->where(sprintf('%s = ?', _GROUP::SET_ID->column()))
            ->andWhere(sprintf('%s = ?', _GROUP::ID->column()))
            ->setParameter(0, $setKey, PDO::PARAM_INT)
            ->setParameter(1, $groupKey, PDO::PARAM_INT)
            ->executeQuery()
            ->fetchAssociative();
        return $result !== false;
    }
}