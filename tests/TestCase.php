<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Factory\EavFactory;
use Faker\Generator;
use Illuminate\Database\Capsule\Manager as Capsule;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected EavFactory $eavFactory;
    protected Generator $faker;
    protected function setUp() : void
    {
        parent::setUp();
        $dbParams = [
            'driver' => 'pdo_sqlite',
            'path' => dirname(__DIR__) . '/tests/test.sqlite'
        ];
        Connection::get($dbParams);
        Connection::pdo("sqlite:".$dbParams['path']);

        $migrator = new Migrator();
        $migrator->rollback();
        $migrator->migrate();
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => __DIR__.'/test.sqlite',
        ]);
        $this->capsule = $capsule;
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $this->eavFactory = new EavFactory();
        $this->faker = \Faker\Factory::create();
    }

    protected function tearDown(): void
    {
        $migrator = new Migrator();
        $migrator->rollback();
        Cleaner::run();
        parent::tearDown();
    }

    protected function getFactoryDefaultConfig(): array
    {
        return [
            ATTR_TYPE::STRING->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::STRING->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::STRING->randomValue(),
                ]
            ],
            ATTR_TYPE::INTEGER->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::INTEGER->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::INTEGER->randomValue(),
                ]
            ],
            ATTR_TYPE::DECIMAL->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::DECIMAL->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::DECIMAL->randomValue(),
                ]
            ],
            ATTR_TYPE::DATETIME->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::DATETIME->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::DATETIME->randomValue(),
                ]
            ],
            ATTR_TYPE::TEXT->value() => [
                ATTR_FACTORY::ATTRIBUTE->field() => [
                    _ATTR::NAME->column() => ATTR_TYPE::TEXT->value(),
                    _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value(),
                    _ATTR::DEFAULT_VALUE->column() => ATTR_TYPE::TEXT->randomValue(),
                ]
            ]
        ];
    }

}