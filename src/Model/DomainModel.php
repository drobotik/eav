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
use Drobotik\Eav\Enum\_DOMAIN;

class DomainModel extends Model
{
    public function __construct()
    {
        $this->setTable(_DOMAIN::table());
        $this->setPrimaryKey(_DOMAIN::ID);
    }
    /**
     * @throws Exception
     */
    public function create(array $data) : int
    {
        $conn = $this->db();
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (:name)",
            _DOMAIN::table(),
            _DOMAIN::NAME
        );
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $data[_DOMAIN::NAME]);
        $stmt->execute();
        return (int) $conn->lastInsertId();
    }

}