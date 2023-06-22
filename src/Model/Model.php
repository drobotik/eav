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
use PDO;

class Model
{
    protected int $key;
    protected string $keyName = 'id';

    protected string $table;

    public function connection() : PDO
    {
        return Connection::pdo();
    }

    public function isKey() : bool
    {
        return isset($this->key) && $this->key > 0;
    }

    public function setKey($key): void
    {
        $this->key = $key;
    }

    public function getKey(): int
    {
        return $this->key;
    }

    public function setKeyName(string $name): void
    {
        $this->keyName = $name;
    }

    public function getKeyName(): string
    {
        return $this->keyName;
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
        $conn = $this->connection();
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

        $key = (int) $conn->lastInsertId();
        $this->setKey($key);

        return $key;
    }

    public function update(array $data) : bool
    {
        $key =  $this->getKey();
        $keyName = $this->getKeyName();

        $conn = $this->connection();
        $table = $this->getTable();

        // Prepare the update statement
        $columns = array_keys($data);
        $setValues = implode(' = ?, ', $columns) . ' = ?';

        $stmt = $conn->prepare("UPDATE $table SET $setValues WHERE $keyName = :key");

        // Bind the data parameters
        $index = 1;
        foreach ($data as $value) {
            $stmt->bindValue($index, $value);
            $index++;
        }

        // Bind the ID parameter
        $stmt->bindValue(':key',  $key, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(): bool
    {
        $key =  $this->getKey();
        $keyName = $this->getKeyName();

        $conn = $this->connection();
        $table = $this->getTable();

        $stmt = $conn->prepare("DELETE FROM $table WHERE $keyName = :key");
        $stmt->bindValue(':key',  $key, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function toArray() : array
    {
        $result = [];
        if($this->isKey())
        {
            $result[$this->getKeyName()] = $this->getKey();
        }
        return $result;
    }

    public function count() : int
    {
        $conn = $this->connection();
        $table = $this->getTable();
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table");
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $record["count"];
    }

    public function findMe()
    {
        $conn = $this->connection();
        $table = $this->getTable();
        $key = $this->getKey();
        $keyName = $this->getKeyName();
        $stmt = $conn->prepare("SELECT * FROM $table WHERE $keyName = :key");
        $stmt->bindValue(':key', $key, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}