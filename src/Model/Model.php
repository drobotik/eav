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
use Doctrine\DBAL\Connection as DBALConnection;
use PDO;

class Model
{
    protected string $primaryKey = 'id';

    protected string $table;

    public function db() : DBALConnection
    {
        return Connection::get();
    }

    public function getNativeConnection() : PDO
    {
        return Connection::getNativeConnection();
    }

    public function setPrimaryKey(string $name): void
    {
        $this->primaryKey = $name;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function getTable() : string
    {
        return $this->table;
    }

    public function insert(array $data): int
    {
        $conn = $this->db()->getNativeConnection();
        $table = $this->getTable();

        // Extract the column names and values from the data array
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        // Bind the values from the $data array to the prepared statement
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();

        return (int) $conn->lastInsertId();
    }

    public function updateByArray(int $key, array $data) : bool
    {
        $keyName = $this->getPrimaryKey();

        $conn = $this->db()->getNativeConnection();
        $table = $this->getTable();

        $setValues = [];
        foreach (array_keys($data) as $column) {
            $setValues[] = "$column = :$column";
        }
        $setValues = implode(", ", $setValues);

        $stmt = $conn->prepare("UPDATE $table SET $setValues WHERE $keyName = :key");

        foreach ($data as $k => $value) {
            $stmt->bindValue($k, $value);
        }

        $binds = array_merge($data, ['key' => $key]);
        return $stmt->execute($binds);
    }

    public function deleteByKey(int $key): bool
    {
        $keyName = $this->getPrimaryKey();

        $conn = $this->db()->getNativeConnection();
        $table = $this->getTable();

        $stmt = $conn->prepare("DELETE FROM $table WHERE $keyName = :key");
        $stmt->bindValue(':key',  $key, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function count() : int
    {
        $conn = $this->db()->getNativeConnection();
        $table = $this->getTable();
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table");
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $record["count"];
    }

    public function findByKey(int $key) : array|false
    {
        return $this->db()
            ->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->where(sprintf('%s = ?', $this->getPrimaryKey()))
            ->setParameter(0, $key, PDO::PARAM_INT)
            ->executeQuery()
            ->fetchAssociative();
    }
}