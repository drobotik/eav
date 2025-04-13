<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Model;

use Exception;
use Kuperwood\Eav\Database\Connection;
use Kuperwood\Eav\Enum\_ENTITY;
use Kuperwood\Eav\Exception\EntityException;
use Kuperwood\Eav\Traits\SingletonsTrait;
use PDO;

class EntityModel extends Model
{
    use SingletonsTrait;

    public function __construct()
    {
        $this->setTable(_ENTITY::table());
        $this->setPrimaryKey(_ENTITY::ID);
    }

    public function getServiceKey(): int
    {
        $key = $this->makeFakerGenerator()->randomDigit();
        return $this->isServiceKey($key)
            ? $this->getServiceKey()
            : $key;
    }

    /**
     * @throws Exception
     */
    public function create(array $data) : int
    {
        $conn = $this->db();
        $sql = sprintf(
            "INSERT INTO %s (%s, %s) VALUES (:domain_id, :attr_set_id)",
            $this->getTable(),
            _ENTITY::DOMAIN_ID,
            _ENTITY::ATTR_SET_ID
        );
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':domain_id', $data[_ENTITY::DOMAIN_ID]);
        $stmt->bindParam(':attr_set_id', $data[_ENTITY::ATTR_SET_ID]);
        $stmt->execute();
        return (int) $conn->lastInsertId();
    }

    public function isServiceKey(int $key) : bool
    {
        $table = $this->getTable();
        $serviceKeyCol = _ENTITY::SERVICE_KEY;

        $conn = $this->db();

        $stmt = $conn->prepare("SELECT count(*) as c FROM $table WHERE $serviceKeyCol = :key");
        $stmt->bindParam(':key', $key);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['c'] > 0;
    }

    public function getByServiceKey(int $key)
    {
        $table = $this->getTable();
        $serviceKeyCol = _ENTITY::SERVICE_KEY;

        $conn = $this->db();

        $stmt = $conn->prepare("SELECT * FROM $table WHERE $serviceKeyCol = :key");
        $stmt->bindParam(':key', $key);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @throws EntityException
     */
    public function bulkCreate(int $amount, int $domainKey, int $setKey, int $serviceKey) : void
    {
        if ($amount < 1)
        {
            EntityException::mustBePositiveAmount();
        }
        $format = "($domainKey, $setKey, $serviceKey)";
        $bulk = [];
        for($i=0;$i<$amount;$i++) {
            $bulk[] = $format;
        }
        $template = sprintf(
            "INSERT INTO "._ENTITY::table()." ("._ENTITY::DOMAIN_ID.", "._ENTITY::ATTR_SET_ID.", "._ENTITY::SERVICE_KEY.") VALUES %s;",
            implode(',',$bulk)
        );

        $conn = $this->db();

        $conn->exec($template);
    }

    public function getBySetAndDomain(int $domainKey, int $setKey) : array
    {
        $conn = Connection::get();
        $table = $this->getTable();

        $stmt = $conn->prepare(
            "SELECT * FROM $table WHERE " . _ENTITY::DOMAIN_ID . " = :domainKey AND " . _ENTITY::ATTR_SET_ID . " = :setKey"
        );

        $stmt->bindParam(':domainKey', $domainKey, PDO::PARAM_INT);
        $stmt->bindParam(':setKey', $setKey, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}