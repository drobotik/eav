<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Commands;

use Doctrine\Migrations\Tools\Console\Command\DoctrineCommand;
use Doctrine\Migrations\Tools\Console\Exception\SchemaDumpRequiresNoMigrations;
use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Enum\_ATTR;
use Drobotik\Eav\Enum\ATTR_FACTORY;
use Drobotik\Eav\Enum\ATTR_TYPE;
use Drobotik\Eav\Factory\EavFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\Migrator;

class GenerateDataset extends DoctrineCommand
{
    protected static $defaultName = 'generate:twenty-thousands-dataset';

    /**
     * @throws SchemaDumpRequiresNoMigrations
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        Connection::get([
            'driver' => 'pdo_sqlite',
            'path' => dirname(__DIR__, 2) . '/tests/large.sqlite'
        ]);
        $migrator = new Migrator();
        $migrator->rollback();
        $migrator->migrate();

        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => dirname(__DIR__, 2) . '/tests/large.sqlite'
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $eavFactory = new EavFactory();
        $domainRecord = $eavFactory->createDomain();
        $set = $eavFactory->createAttributeSet();
        $group = $eavFactory->createGroup($set->getKey());

        $iterations = 20000;
        $this->io->progressStart($iterations);
        for($i=0; $i < $iterations; $i++) {
            $string = ATTR_TYPE::STRING->randomValue();
            $integer = ATTR_TYPE::INTEGER->randomValue();
            $decimal = ATTR_TYPE::DECIMAL->randomValue();
            $datetime = ATTR_TYPE::DATETIME->randomValue($i);
            $text = ATTR_TYPE::TEXT->randomValue();
            $config = [
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::STRING->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group->getKey(),
                    ATTR_FACTORY::VALUE->field() => $string
                ],
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::INTEGER->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group->getKey(),
                    ATTR_FACTORY::VALUE->field() => $integer
                ],
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::DECIMAL->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::DECIMAL->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group->getKey(),
                    ATTR_FACTORY::VALUE->field() => $decimal
                ],
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::DATETIME->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::DATETIME->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group->getKey(),
                    ATTR_FACTORY::VALUE->field() => $datetime
                ],
                [
                    ATTR_FACTORY::ATTRIBUTE->field() => [
                        _ATTR::NAME->column() => ATTR_TYPE::TEXT->value(),
                        _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
                    ],
                    ATTR_FACTORY::GROUP->field() => $group->getKey(),
                    ATTR_FACTORY::VALUE->field() => $text
                ]
            ];
            $eavFactory->createEavEntity($config, $domainRecord->getKey(), $set->getKey());
            $this->io->progressAdvance();
        }
        $this->io->progressFinish();
        return 0;
    }
}