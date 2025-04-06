<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Model;

use Doctrine\DBAL\Exception;
use Kuperwood\Eav\Enum\_PIVOT;
use PDO;

class PivotModel extends Model
{
    public function __construct()
    {
        $this->setTable(_PIVOT::table());
        $this->setPrimaryKey(_PIVOT::ID);
    }

    /**
     * @throws Exception
     */
    public function create(array $data) : int
    {
        $conn = $this->db();
        $sql = sprintf(
            "INSERT INTO %s (%s, %s, %s, %s) VALUES (:domain_id, :set_id, :group_id, :attr_id)",
            $this->getTable(),
            _PIVOT::DOMAIN_ID,
            _PIVOT::SET_ID,
            _PIVOT::GROUP_ID,
            _PIVOT::ATTR_ID
        );
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':domain_id', $data[_PIVOT::DOMAIN_ID]);
        $stmt->bindParam(':set_id', $data[_PIVOT::SET_ID]);
        $stmt->bindParam(':group_id', $data[_PIVOT::GROUP_ID]);
        $stmt->bindParam(':attr_id', $data[_PIVOT::ATTR_ID]);
        $stmt->execute();
        return (int) $conn->lastInsertId();
    }

    public function findOne(int $domainKey, $setKey, $groupKey, $attributeKey)
    {
        $conn = $this->db();
        $sql = sprintf(
            "SELECT %s FROM %s WHERE %s = :domain_id AND %s = :set_id AND %s = :group_id AND %s = :attr_id",
            $this->getPrimaryKey(),
            $this->getTable(),
            _PIVOT::DOMAIN_ID,
            _PIVOT::SET_ID,
            _PIVOT::GROUP_ID,
            _PIVOT::ATTR_ID
        );
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':domain_id', $domainKey, PDO::PARAM_INT);
        $stmt->bindParam(':set_id', $setKey, PDO::PARAM_INT);
        $stmt->bindParam(':group_id', $groupKey, PDO::PARAM_INT);
        $stmt->bindParam(':attr_id', $attributeKey, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}