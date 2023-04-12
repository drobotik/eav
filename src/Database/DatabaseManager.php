<?php

namespace Drobotik\Eav\Database;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\Mapping\Driver\StaticPHPDriver;

class DatabaseManager
{
    private static ?EntityManager $entityManager = null;
    /**
     * @throws MissingMappingDriverImplementation
     */
    public static function initialize() : EntityManager
    {
        if(is_null(static::$entityManager)) {
            $conn = Connection::getConnection();
            $config = ORMSetup::createConfiguration(true);
            $config->setMetadataDriverImpl(new StaticPHPDriver(__DIR__."/Model"));
            static::$entityManager = new EntityManager($conn, $config);
        }
        return static::$entityManager;
    }
}