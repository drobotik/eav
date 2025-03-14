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
        $this->setPrimaryKey(_ATTR::ID);
    }

    public function create(array $data) : int
    {
        $conn = $this->db();

        // Use sprintf to directly create column names and placeholders
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->getTable(),
            implode(', ', [
                _ATTR::DOMAIN_ID,
                _ATTR::NAME,
                _ATTR::TYPE,
                _ATTR::STRATEGY,
                _ATTR::SOURCE,
                _ATTR::DEFAULT_VALUE,
                _ATTR::DESCRIPTION
            ]),
            implode(', ', [
                sprintf(':%s', _ATTR::DOMAIN_ID),
                sprintf(':%s', _ATTR::NAME),
                sprintf(':%s', _ATTR::TYPE),
                sprintf(':%s', _ATTR::STRATEGY),
                sprintf(':%s', _ATTR::SOURCE),
                sprintf(':%s', _ATTR::DEFAULT_VALUE),
                sprintf(':%s', _ATTR::DESCRIPTION)
            ])
        );

        // Prepare the PDO statement
        $stmt = $conn->prepare($sql);

        // Bind the parameters using the names directly
        $stmt->bindParam(sprintf(':%s', _ATTR::DOMAIN_ID), $data[_ATTR::DOMAIN_ID], PDO::PARAM_INT);
        $stmt->bindParam(sprintf(':%s', _ATTR::NAME), $data[_ATTR::NAME]);
        $stmt->bindParam(sprintf(':%s', _ATTR::TYPE), $data[_ATTR::TYPE]);
        $stmt->bindParam(sprintf(':%s', _ATTR::STRATEGY), $data[_ATTR::STRATEGY]);
        $stmt->bindParam(sprintf(':%s', _ATTR::SOURCE), $data[_ATTR::SOURCE]);
        $stmt->bindParam(sprintf(':%s', _ATTR::DEFAULT_VALUE), $data[_ATTR::DEFAULT_VALUE]);
        $stmt->bindParam(sprintf(':%s', _ATTR::DESCRIPTION), $data[_ATTR::DESCRIPTION]);

        // Execute the query
        $stmt->execute();

        // Return the last inserted ID
        return (int) $conn->lastInsertId();
    }

    public function findByName(string $name, int $domainKey)
    {
        $conn = $this->db();

        // Prepare the SQL query with placeholders
        $sql = sprintf(
            "SELECT %s FROM %s WHERE %s = :domain_key AND %s = :name",
            $this->getPrimaryKey(),
            $this->getTable(),
            _ATTR::DOMAIN_ID,
            _ATTR::NAME
        );

        // Prepare the PDO statement
        $stmt = $conn->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':domain_key', $domainKey, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
