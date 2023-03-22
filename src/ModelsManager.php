<?php

namespace Kuperwood\Eav;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\Mapping\Driver\StaticPHPDriver;

class ModelsManager
{
    private static ?EntityManager $entityManager = null;

    public static function getMe() : EntityManager
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