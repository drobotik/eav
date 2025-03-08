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
use Drobotik\Eav\Trait\SingletonsTrait;
use InvalidArgumentException;

class ValueBase extends Model
{

    use SingletonsTrait;
    public function __construct()
    {
        $this->setPrimaryKey(_VALUE::ID);
    }

    public function find(string $table, int $domainKey, int $entityKey, int $attributeKey) : bool|array
    {
        return $this->db()
            ->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->where(sprintf('%s = :domain AND %s = :entity AND %s = :attr',
                _VALUE::DOMAIN_ID, _VALUE::ENTITY_ID, _VALUE::ATTRIBUTE_ID
            ))
            ->setParameters([
                "domain" => $domainKey,
                "entity" => $entityKey,
                "attr" => $attributeKey
            ])
            ->executeQuery()
            ->fetchAssociative();
    }

    public function create(string $table, int $domainKey, int $entityKey, int $attributeKey, $value) : int
    {
        $conn = $this->db();

        $conn->createQueryBuilder()
            ->insert($table)
            ->values([
                _VALUE::DOMAIN_ID => '?',
                _VALUE::ENTITY_ID => '?',
                _VALUE::ATTRIBUTE_ID => '?',
                _VALUE::VALUE => '?',
            ])
            ->setParameter(0, $domainKey)
            ->setParameter(1, $entityKey)
            ->setParameter(2, $attributeKey)
            ->setParameter(3, $value)
            ->executeQuery();
        return (int) $conn->lastInsertId();
    }

    public function update(string $table, int $domainKey, int $entityKey, int $attributeKey, $value) : int
    {
        $conn = $this->db();
        return $conn->createQueryBuilder()
            ->update($table)
            ->where(sprintf('%s = :domain AND %s = :entity AND %s = :attr',
                _VALUE::DOMAIN_ID, _VALUE::ENTITY_ID, _VALUE::ATTRIBUTE_ID
            ))
            ->set(_VALUE::VALUE, ':value')
            ->setParameters([
                "domain" => $domainKey,
                "entity" => $entityKey,
                "attr" => $attributeKey,
                "value" => $value
            ])
            ->executeQuery()
            ->rowCount();
    }

    public function destroy(string $table, int $domainKey, int $entityKey, int $attributeKey) : int
    {
        return $this->db()
            ->createQueryBuilder()
            ->delete($table)
            ->where(sprintf('%s = ? AND %s = ? AND %s = ?',
                _VALUE::DOMAIN_ID, _VALUE::ENTITY_ID, _VALUE::ATTRIBUTE_ID
            ))
            ->setParameters([$domainKey, $entityKey, $attributeKey])
            ->executeQuery()
            ->rowCount();
    }

    public function bulkCreate(ValueSet $valueSet, int $domainKey): void
    {
        $pdo = Connection::get()->getNativeConnection();

        $template = "INSERT INTO %s ("._VALUE::DOMAIN_ID.","._VALUE::ENTITY_ID.","._VALUE::ATTRIBUTE_ID.","._VALUE::VALUE.")";

        $stringTable = ATTR_TYPE::valueTable(ATTR_TYPE::STRING);
        $integerTable = ATTR_TYPE::valueTable(ATTR_TYPE::INTEGER);
        $decimalTable = ATTR_TYPE::valueTable(ATTR_TYPE::DECIMAL);
        $datetimeTable = ATTR_TYPE::valueTable(ATTR_TYPE::DATETIME);
        $textTable = ATTR_TYPE::valueTable(ATTR_TYPE::TEXT);

        $stringTemplate = sprintf($template, $stringTable) . " VALUES %s;";
        $integerTemplate = sprintf($template, $integerTable) . " VALUES %s;";
        $decimalTemplate = sprintf($template, $decimalTable) . " VALUES %s;";
        $datetimeTemplate = sprintf($template, $datetimeTable) . " VALUES %s;";
        $textTemplate = sprintf($template, $textTable) . " VALUES %s;";

        $stringBulk = [];
        $integerBulk = [];
        $decimalBulk = [];
        $datetimeBulk = [];
        $textBulk = [];

        foreach ($valueSet->forExistingEntities() as $data) {
            $attributeKey = $data->getAttributeKey();
            $value = $data->getValue();
            $table = ATTR_TYPE::valueTable($data->getType());
            $entityKey = $data->getEntityKey();
            $bulkTemplate = "($domainKey, $entityKey, $attributeKey, '$value')";
            switch ($table) {
                case $stringTable:
                    $stringBulk[] = $bulkTemplate;
                    break;

                case $integerTable:
                    $integerBulk[] = $bulkTemplate;
                    break;

                case $decimalTable:
                    $decimalBulk[] = $bulkTemplate;
                    break;

                case $datetimeTable:
                    $datetimeBulk[] = $bulkTemplate;
                    break;

                case $textTable:
                    $textBulk[] = $bulkTemplate;
                    break;

                default:
                    throw new InvalidArgumentException("Unhandled table: " . $table);
            }
        }

        if(count($stringBulk) > 0) {
            $pdo->exec(sprintf($stringTemplate, implode(',', $stringBulk)));
        }
        if(count($integerBulk) > 0) {
            $pdo->exec(sprintf($integerTemplate, implode(',', $integerBulk)));
        }
        if(count($decimalBulk) > 0) {
            $pdo->exec(sprintf($decimalTemplate, implode(',', $decimalBulk)));
        }
        if(count($datetimeBulk) > 0) {
            $pdo->exec(sprintf($datetimeTemplate, implode(',', $datetimeBulk)));
        }
        if(count($textBulk) > 0) {
            $pdo->exec(sprintf($textTemplate, implode(',', $textBulk)));
        }
    }

}