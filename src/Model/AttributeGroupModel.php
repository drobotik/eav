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
        $this->setPrimaryKey(_GROUP::ID);
    }


    public function create(array $data) : int
    {
        $conn = $this->getNativeConnection();
        $sql = sprintf("INSERT INTO %s (%s, %s) values(:sid, :name)", _GROUP::table(), _GROUP::SET_ID, _GROUP::NAME);
        $stmt = $conn->prepare($sql);
        $stmt->execute(['sid' => $data[_GROUP::SET_ID], 'name' => $data[_GROUP::NAME]]);
        return (int) $conn->lastInsertId();
    }

    public function checkGroupInAttributeSet(int $setKey, int $groupKey) : bool
    {
        $result = $this->db()->createQueryBuilder()
            ->select($this->getPrimaryKey())
            ->from($this->getTable())
            ->where(sprintf('%s = ?', _GROUP::SET_ID))
            ->andWhere(sprintf('%s = ?', _GROUP::ID))
            ->setParameter(0, $setKey, PDO::PARAM_INT)
            ->setParameter(1, $groupKey, PDO::PARAM_INT)
            ->executeQuery()
            ->fetchAssociative();
        return $result !== false;
    }
}