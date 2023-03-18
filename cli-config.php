<?php

use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Kuperwood\Eav\Connection;

$config = new PhpFile('migrations.php'); // Or use one of the Doctrine\Migrations\Configuration\Configuration\* loaders
$conn = Connection::getConnection();
return DependencyFactory::fromConnection($config, new ExistingConnection($conn));