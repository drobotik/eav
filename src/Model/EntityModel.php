<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Model;

use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Exception\EntityException;
use Drobotik\Eav\Trait\SingletonsTrait;
use PDO;

class EntityModel extends Model
{
    use SingletonsTrait;

    private int $domainKey;
    private int $setKey;

    public function __construct()
    {
        $this->setTable(_ENTITY::table());
        $this->setKeyName(_ENTITY::ID->column());
    }

    public function getDomainKey(): int
    {
        return $this->domainKey;
    }

    public function setDomainKey(int $key) : self
    {
        $this->domainKey = $key;
        return $this;
    }

    public function setSetKey(int $key) : self
    {
        $this->setKey = $key;
        return $this;
    }

    public function getSetKey(): int
    {
        return $this->setKey;
    }

    public function getServiceKey(): int
    {
        $key = $this->makeFakerGenerator()->randomDigit();
        return $this->isServiceKey($key)
            ? $this->getServiceKey()
            : $key;
    }

    public function create() : int
    {
        return $this->insert([
            _ENTITY::DOMAIN_ID->column() => $this->getDomainKey(),
            _ENTITY::ATTR_SET_ID->column() => $this->getSetKey()
        ]);
    }

    public function toArray(): array
    {
        $result = [];
        if(isset($this->domainKey))
            $result[_ENTITY::DOMAIN_ID->column()] = $this->getDomainKey();
        if(isset($this->setKey))
            $result[_ENTITY::ATTR_SET_ID->column()] = $this->getSetKey();
        return $result;
    }

    public function isServiceKey(int $key) : bool
    {
        $table = $this->getTable();
        $serviceKeyCol = _ENTITY::SERVICE_KEY->column();

        $conn = $this->db()->getNativeConnection();

        $stmt = $conn->prepare("SELECT count(*) as c FROM $table WHERE $serviceKeyCol = :key");
        $stmt->bindParam(':key', $key);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['c'] > 0;
    }

    public function getByServiceKey(int $key): bool|array
    {
        $table = $this->getTable();
        $serviceKeyCol = _ENTITY::SERVICE_KEY->column();

        $conn = $this->db()->getNativeConnection();;

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
            "INSERT INTO "._ENTITY::table()." ("._ENTITY::DOMAIN_ID->column().", "._ENTITY::ATTR_SET_ID->column().", "._ENTITY::SERVICE_KEY->column().") VALUES %s;",
            implode(',',$bulk)
        );

        $conn = $this->db()->getNativeConnection();

        $conn->exec($template);
    }

    public function getBySetAndDomain() : array
    {
        return $this->db()->createQueryBuilder()
            ->select('*')
            ->from(_ENTITY::table())
            ->where(sprintf('%s = %s', _ENTITY::DOMAIN_ID->column(), $this->getDomainKey()))
            ->andWhere(sprintf('%s = %s', _ENTITY::ATTR_SET_ID->column(), $this->getSetKey()))
            ->executeQuery()
            ->fetchAllAssociative();
    }
}