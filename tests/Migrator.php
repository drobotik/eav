<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Drobotik\Eav\Database\Connection;
use Symfony\Component\Console\Input\ArrayInput;

class Migrator
{
    const FIRST = 'first';
    const LATEST = 'latest';

    public function migrate() : void
    {
        $this->run(self::LATEST);
    }

    public function rollback() : void
    {
        $this->run(self::FIRST);
    }

    private function run(string $type): void
    {
        $config = new PhpFile(__DIR__.'/../migrations.php');
        $conn = Connection::getConnection();
        $di = DependencyFactory::fromConnection($config, new ExistingConnection($conn));
        $migrator = $di->getMigrator();
        $version = $di->getVersionAliasResolver()->resolveVersionAlias($type);
        $planCalculator = $di->getMigrationPlanCalculator();
        $plan = $planCalculator->getPlanUntilVersion($version);
        $migratorConfigurationFactory = $di->getConsoleInputMigratorConfigurationFactory();
        $migratorConfiguration = $migratorConfigurationFactory->getMigratorConfiguration(new ArrayInput([]));
        $di->getMetadataStorage()->ensureInitialized();
        $migrator->migrate($plan, $migratorConfiguration);
    }
}