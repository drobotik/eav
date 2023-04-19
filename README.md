# EAV package

A tool to manage and maintain EAV master data across multiple domains.

## Requirements
- PHP >=8.1
- Illuminate\database ^9.0|^10.0
- Illuminate\validator ^9.0|^10.0
- Illuminate\translation ^9.0|^10.0

## Getting started
Composer
```bash
composer require drobotik/eav
```
If you would like to work with lib.
```bash
$ git clone git@github.com:drobotik/eav.git 
$ cd eav
$ composer install
# check cli app output
$ php eav 
```

### Database connection
EAV uses Laravel Capsule for connections.
For Laravel apps, it's trying to use default connection and can be used as is.
For other cases, initialize a Capsule instance:
```php
$capsule = new Capsule;
$capsule->addConnection([
    // connection settings
]);
$this->capsule = $capsule;
$capsule->setAsGlobal();
$capsule->bootEloquent();
```

### Work with migrations, cli-application
CLI app relies on a database connection. It uses [Doctrine DBAL](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#configuration) to perform connections.<br />
There isn't any pre-configured connection information. For non-docker environments is required to set up a connection. 
As an option, make a new file connection.php in root folder. This file will be automatically included.
```php
#connection.php
use Drobotik\Eav\Database\DoctrineConnection;
$config = [
    // connection settings
]
$connection = DoctrineConnection::getConnection($config)
```
Note use a [driver](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#driver) that suits your needs.

Migrate:
```bash
$ php eav migrations:migrate latest
```


### Laravel migrations support
It also contains laravel migrations for publishing on Laravel app. Add EavServiceProvider to
providers config array. 
```php
\Drobotik\Eav\Database\Support\Laravel\EavServiceProvider::class
```
Publish migrations by vendor:publish.
### Raw schema dump
Use dump.sql placed on root folder.

## Docs 

Documentation in progress..

## Example


```php
// make data
$domain = $this->eavFactory->createDomain();
$attrSet = $this->eavFactory->createAttributeSet($domain);
$group = $this->eavFactory->createGroup($attrSet);
$stringAttribute = $this->eavFactory->createAttribute($domain, [
    _ATTR::NAME->column() => "name",
    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
]);
$integerAttribute = $this->eavFactory->createAttribute($domain, [
    _ATTR::NAME->column() => "age",
    _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
]);
$this->eavFactory->createPivot($domain, $attrSet, $group, $stringAttribute);
$this->eavFactory->createPivot($domain, $attrSet, $group, $integerAttribute);
$data = [
    "name" => $this->faker->name,
    "age" => $this->faker->randomNumber(),
];
// create new 
$entity = new Entity();
$entity->setDomainKey($domain->getKey());
$entity->getAttributeSet()->setKey($attrSet->getKey());
$entity->getBag()->setFields($data);
$result = $entity->save();
$id = $entity->getKey()
// find
$entity = new Entity();
$entity->find($id);
$data = $entity->toArray();
// update
$entity->getBag()->setField("name", "new name")
$result = $entity->save();
// delete
$entity->delete();
```

## Planned features 

* Domain import/export (csv/excel)
* Option to use separated tables for ValueModels 
* Attribute props 

## Contributing

Contributions are welcome. 
Please note the following guidelines before submitting your pull request.

- Follow [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standards
- Create feature branches, one pull request per feature
- Implement your change and add tests for it
- Ensure the test suite passes

## License

Eav package is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2023 [Aleksandr Drobotik](https://github.com/drobotik)