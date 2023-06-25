<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Drobotik\Eav\Enum\_ATTR;
use PDO;

class AttributeModel extends Model
{
    public function __construct()
    {
        $this->setTable(_ATTR::table());
        $this->setPrimaryKey(_ATTR::ID->column());
    }

    public function create(array $data) : int
    {
        $conn = $this->db();
        $conn->createQueryBuilder()
            ->insert($this->getTable())
            ->values([
                _ATTR::DOMAIN_ID->column() => '?',
                _ATTR::NAME->column() => '?',
                _ATTR::TYPE->column() => '?',
                _ATTR::STRATEGY->column() => '?',
                _ATTR::SOURCE->column() => '?',
                _ATTR::DEFAULT_VALUE->column() => '?',
                _ATTR::DESCRIPTION->column() => '?'
            ])
            ->setParameter(0, $data[_ATTR::DOMAIN_ID->column()], PDO::PARAM_INT)
            ->setParameter(1, $data[_ATTR::NAME->column()])
            ->setParameter(2, $data[_ATTR::TYPE->column()])
            ->setParameter(3, $data[_ATTR::STRATEGY->column()])
            ->setParameter(4, $data[_ATTR::SOURCE->column()])
            ->setParameter(5, $data[_ATTR::DEFAULT_VALUE->column()])
            ->setParameter(6, $data[_ATTR::DESCRIPTION->column()])
            ->executeQuery();
        return (int) $conn->lastInsertId();
    }

    public function findByName(string $name, int $domainKey) : bool|array
    {
        return $this->db()->createQueryBuilder()
            ->select($this->getPrimaryKey())
            ->from($this->getTable())
            ->where(sprintf('%s = ?', _ATTR::DOMAIN_ID->column()))
            ->andWhere(sprintf('%s = ?', _ATTR::NAME->column()))
            ->setParameter(0, $domainKey, PDO::PARAM_INT)
            ->setParameter(1, $name)
            ->executeQuery()
            ->fetchAssociative();
    }

}
