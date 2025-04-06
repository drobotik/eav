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
use Kuperwood\Eav\Enum\_ATTR;
use Kuperwood\Eav\Enum\_PIVOT;
use Kuperwood\Eav\Enum\_SET;
use PDO;

class AttributeSetModel extends Model
{
    public function __construct()
    {
        $this->setTable(_SET::table());
        $this->setPrimaryKey(_SET::ID);
    }

    public function create(array $data) : int
    {
        $conn = $this->db();
        $sql = sprintf(
            "INSERT INTO %s (%s, %s) VALUES (:domain_id, :name)",
            $this->getTable(),
            _SET::DOMAIN_ID,
            _SET::NAME
        );
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':domain_id', $data[_SET::DOMAIN_ID]);
        $stmt->bindParam(':name', $data[_SET::NAME]);
        $stmt->execute();
        return (int) $conn->lastInsertId();
    }
    /**
     * @throws Exception
     */
    public function findAttributes($domainKey, $setKey = null) : array
    {
        $sql = sprintf(
            "SELECT a.* FROM %s a
        INNER JOIN %s p ON a.%s = p.%s
        WHERE p.%s = :domain_id",
            _ATTR::table(), _PIVOT::table(),
            _ATTR::ID, _PIVOT::ATTR_ID,
            _PIVOT::DOMAIN_ID
        );

        if (!is_null($setKey)) {
            $sql .= sprintf(" AND p.%s = :set_id", _PIVOT::SET_ID);
        }

        $conn = $this->db();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':domain_id', $domainKey, PDO::PARAM_INT);

        if (!is_null($setKey)) {
            $stmt->bindParam(':set_id', $setKey, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}