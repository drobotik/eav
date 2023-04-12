# EAV package

This package is tool to manage and maintain EAV master data across multiple domains. 

## Requirements
- PHP >=8.1

## Getting started

Clone repository.
```bash
$ git clone git@github.com:drobotik/eav.git 
$ cd eav
```

Eav package relies on a database connection. It uses [Doctrine DBAL](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#configuration) to perform connections.

There isn't any pre-configured connection information. You may set up a connection by providing credentials in an array.

```php
use Drobotik\Eav\Database\Connection;
$config = [
    'driver' => "pdo_mysql",
    'host' => "mysql",
    'port' => "3306",
    'dbname' => "eav",
    'user' => "eav",
    'password' => "eav"
]
$connection = Connection::getConnection($config)
```

## Docs 

...

## Planed features 

...

## Contributing!

Contributions are welcome. 
Please note the following guidelines before submitting your pull request.

- Follow [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standards.
- Create feature branches, one pull request per feature
- Implement your change and add tests for it.
- Ensure the test suite passes.

## License

Eav package is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2023 [Aleksandr Drobotik](https://github.com/drobotik)