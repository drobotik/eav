# EAV package

Manage, manipulate EAV data across multiple domains. CRUD/import/export entities, manage attribute sets and groups.
Configurable attributes, attribute strategies with hooks.
It's ideas from "Magento way". App oriented for custom CMS. 

Currently, it dependent from Illuminate\database Laravel models, they are used for this structure tables: 
domain, entity, attribute_set, attribute_group, attribute, pivot, 
string_value, integer_value, decimal_value, datetime_value, text_value. 

### Features
- single entity CRUD
- import attribute set, bulk create new, update/delete existing, csv driver
- export attribute set, query builder

### Requirements
- PHP >=8.1
- Illuminate\database
- Illuminate\validator ^9.0|^10.0
- Illuminate\translation ^9.0|^10.0

[Documentation](./docs/eav.md)

### Installation
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

### Planned features 

* ~~Domain import/export csv~~
* Attribute props
* removing Illuminate\database dependency, Impl folder

### Contributing

Please note the following guidelines before submitting pull request.

- Follow [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standards
- Create feature branches, one pull request per feature
- Implement your change and add tests for it
- Ensure the test suite passes

### License

Eav package is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2023 [Aleksandr Drobotik](https://github.com/drobotik)