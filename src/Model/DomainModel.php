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
use Kuperwood\Eav\Enum\_DOMAIN;

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