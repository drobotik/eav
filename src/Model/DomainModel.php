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
        $conn->createQueryBuilder()
            ->insert(_DOMAIN::table())
            ->values([
                _DOMAIN::NAME => '?'
            ])
            ->setParameter(0, $data[_DOMAIN::NAME])
            ->executeQuery();
        return (int) $conn->lastInsertId();
    }

}