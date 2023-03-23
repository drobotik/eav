#!/usr/bin/env php
<?php

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\CurrentCommand;
use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\ListCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\RollupCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\SyncMetadataCommand;
use Doctrine\Migrations\Tools\Console\Command\UpToDateCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Kuperwood\Eav\ModelsManager;
use Symfony\Component\Console\Application;

require_once "vendor/autoload.php";

$entityManager = ModelsManager::getMe();
$migrationsConfig = new PhpFile('migrations.php');
$dependencyFactory = DependencyFactory::fromEntityManager($migrationsConfig, new ExistingEntityManager($entityManager));
$migrationCommands = [
    new CurrentCommand($dependencyFactory),
    new DumpSchemaCommand($dependencyFactory),
    new ExecuteCommand($dependencyFactory),
    new GenerateCommand($dependencyFactory),
    new LatestCommand($dependencyFactory),
    new MigrateCommand($dependencyFactory),
    new RollupCommand($dependencyFactory),
    new StatusCommand($dependencyFactory),
    new VersionCommand($dependencyFactory),
    new UpToDateCommand($dependencyFactory),
    new SyncMetadataCommand($dependencyFactory),
    new ListCommand($dependencyFactory),
    new DiffCommand($dependencyFactory)
];

$app = new Application('Kuperwood EAV', 1);
ConsoleRunner::addCommands($app, new SingleManagerProvider($entityManager));
$app->addCommands($migrationCommands);
$app->run();