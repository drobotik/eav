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
use Drobotik\Eav\Enum\_ENTITY;
use Drobotik\Eav\Exception\EntityException;
use Drobotik\Eav\Trait\SingletonsTrait;
use PDO;

class EntityModel extends Model
{
    use SingletonsTrait;


    public function __construct()
    {
        $this->setTable(_ENTITY::table());
        $this->setKeyName(_ENTITY::ID->column());
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
        $conn->createQueryBuilder()
            ->insert($this->getTable())
            ->values([
                _ENTITY::DOMAIN_ID->column() => '?',
                _ENTITY::ATTR_SET_ID->column() => '?'
            ])
            ->setParameter(0, $data[_ENTITY::DOMAIN_ID->column()])
            ->setParameter(1, $data[_ENTITY::ATTR_SET_ID->column()])
            ->executeQuery();
        return (int) $conn->lastInsertId();
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

    public function getBySetAndDomain(int $domainKey, int $setKey) : array
    {
        return $this->db()->createQueryBuilder()
            ->select('*')
            ->from($this->getTable())
            ->where(sprintf('%s = %s', _ENTITY::DOMAIN_ID->column(), $domainKey))
            ->andWhere(sprintf('%s = %s', _ENTITY::ATTR_SET_ID->column(), $setKey))
            ->executeQuery()
            ->fetchAllAssociative();
    }
}