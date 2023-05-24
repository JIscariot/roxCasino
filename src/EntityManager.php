<?php

namespace App;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager as BaseEntityManager;
use Doctrine\ORM\ORMSetup;

class EntityManager
{
    private static ?self $instance = null;

    private BaseEntityManager $entityManager;

    private function __construct()
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/'], $_ENV['APP_DEBUG'],
        );

        $connection = DriverManager::getConnection(
            [
                'path' => __DIR__ . '/../' .$_ENV['DATABASE_FILENAME'], 'driver' => $_ENV['DATABASE_DRIVER']
            ], $config
        );
        $this->entityManager = new BaseEntityManager($connection, $config);
    }

    public static function getInstance(): BaseEntityManager
    {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance->entityManager;
    }
}