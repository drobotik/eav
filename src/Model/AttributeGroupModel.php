<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

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
        $conn = $this->db();
        $sql = sprintf(
            "SELECT %s FROM %s WHERE %s = :set_id AND %s = :group_id",
            $this->getPrimaryKey(),
            $this->getTable(),
            _GROUP::SET_ID,
            _GROUP::ID
        );
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':set_id', $setKey, PDO::PARAM_INT);
        $stmt->bindParam(':group_id', $groupKey, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false;
    }
}