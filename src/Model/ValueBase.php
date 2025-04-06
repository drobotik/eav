<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_VALUE;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Import\Content\ValueSet;
use Drobotik\Eav\Traits\SingletonsTrait;
use Exception;
use InvalidArgumentException;
use PDO;

class ValueBase extends Model
{

    use SingletonsTrait;
    public function __construct()
    {
        $this->setPrimaryKey(_VALUE::ID);
    }

    public function find(string $type, $domainKey, $entityKey, $attributeKey)
    {
        $table = ATTR_TYPE::valueTable($type);

        $conn = $this->db();
        $sql = sprintf(
            "SELECT * FROM %s WHERE %s = :domain AND %s = :entity AND %s = :attr",
            $table,
            _VALUE::DOMAIN_ID,
            _VALUE::ENTITY_ID,
            _VALUE::ATTRIBUTE_ID
        );

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':domain', $domainKey, PDO::PARAM_INT);
        $stmt->bindParam(':entity', $entityKey, PDO::PARAM_INT);
        $stmt->bindParam(':attr', $attributeKey, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @throws Exception
     */
    public function create(string $type, $domainKey, $entityKey, $attributeKey, $value) : int
    {
        $table = ATTR_TYPE::valueTable($type);

        $conn = $this->db();
        $sql = sprintf(
            "INSERT INTO %s (%s, %s, %s, %s) VALUES (:domain_id, :entity_id, :attribute_id, :value)",
            $table,
            _VALUE::DOMAIN_ID,
            _VALUE::ENTITY_ID,
            _VALUE::ATTRIBUTE_ID,
            _VALUE::VALUE
        );

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':domain_id', $domainKey, PDO::PARAM_INT);
        $stmt->bindParam(':entity_id', $entityKey, PDO::PARAM_INT);
        $stmt->bindParam(':attribute_id', $attributeKey, PDO::PARAM_INT);
        $v = $this->makeValueParser()->parse($type, $value);
        $stmt->bindParam(':value',$v);

        $stmt->execute();

        return (int) $conn->lastInsertId();
    }

    public function update(string $type, $domainKey, $entityKey, $attributeKey, $value) : int
    {
        $pdo = Connection::get();
        $table = ATTR_TYPE::valueTable($type);
        $parsedValue = $this->makeValueParser()->parse($type, $value);

        $sql = "UPDATE $table 
            SET " . _VALUE::VALUE . " = :val 
            WHERE " . _VALUE::DOMAIN_ID . " = :domain 
            AND " . _VALUE::ENTITY_ID . " = :entity 
            AND " . _VALUE::ATTRIBUTE_ID . " = :attr";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':val', $parsedValue);
        $stmt->bindParam(':domain', $domainKey, PDO::PARAM_INT);
        $stmt->bindParam(':entity', $entityKey, PDO::PARAM_INT);
        $stmt->bindParam(':attr', $attributeKey, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function destroy(string $type, $domainKey, $entityKey, $attributeKey) : int
    {
        $pdo = Connection::get();
        $table = ATTR_TYPE::valueTable($type);

        $sql = "DELETE FROM $table 
            WHERE " . _VALUE::DOMAIN_ID . " = :domain 
            AND " . _VALUE::ENTITY_ID . " = :entity 
            AND " . _VALUE::ATTRIBUTE_ID . " = :attr";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':domain', $domainKey, PDO::PARAM_INT);
        $stmt->bindParam(':entity', $entityKey, PDO::PARAM_INT);
        $stmt->bindParam(':attr', $attributeKey, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

}