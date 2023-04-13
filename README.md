# EAV package

This package is tool to manage and maintain EAV master data across multiple domains.

## Requirements
- PHP >=8.1

## Getting started

Clone repository.
```bash
$ git clone git@github.com:drobotik/eav.git 
$ cd eav
$ composer install
# check console output
$ php eav 
```
Eav package relies on a database connection. It uses [Doctrine DBAL](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#configuration) to perform connections.<br />
There isn't any pre-configured connection information. You may set up a connection by providing credentials in an array.

```php
#connection.php
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
Note use a [driver](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#driver) that suits your needs.

### Database schema using migrations
If you want to use migrations
Make a file connection.php(it will be automatically included) and set up a connection to the database same way.
Migrate:
```bash
$ php eav migrations:migrate latest
```
### Database schema using sql dump
On the root of the project there is a file dump.sql.

## Docs 

Documentation in progress..

## Example


```php
// make data
$domain = $this->eavFactory->createDomain();
$attrSet = $this->eavFactory->createAttributeSet($domain);
$group = $this->eavFactory->createGroup($attrSet);
$stringAttribute = $this->eavFactory->createAttribute($domain, [
    _ATTR::NAME->column() => "string",
    _ATTR::TYPE->column() => ATTR_TYPE::STRING->value()
]);
$integerAttribute = $this->eavFactory->createAttribute($domain, [
    _ATTR::NAME->column() => "integer",
    _ATTR::TYPE->column() => ATTR_TYPE::INTEGER->value()
]);
$this->eavFactory->createPivot($domain, $attrSet, $group, $stringAttribute);
$this->eavFactory->createPivot($domain, $attrSet, $group, $integerAttribute);
$data = [
    "string" => $this->faker->word,
    "integer" => $this->faker->randomNumber(),
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
$entity->getBag()->setField("string", "newValue")
$result = $entity->save();
// delete
$entity->delete();
```

## Planned features 

* Domain import/export (csv/excel)

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