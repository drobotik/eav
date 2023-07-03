# EAV library

[![Latest Version](https://img.shields.io/github/release/drobotik/eav.svg?style=flat-square)](https://github.com/drobotik/eav/releases)
[![Build Status](https://github.com/drobotik/eav/workflows/tests/badge.svg)](https://github.com/drobotik/eav/actions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://opensource.org/license/mit)
[![Quality Score](https://img.shields.io/scrutinizer/g/drobotik/eav.svg?style=flat-square)](https://scrutinizer-ci.com/g/drobotik/eav)
[![Code Coverage](https://scrutinizer-ci.com/g/drobotik/eav/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/drobotik/eav/?branch=master)

The EAV library is designed to manage and manipulate EAV data across multiple domains. Library offers functionalities for configurable entity CRUD, importing and exporting entities, as well as managing attribute sets and groups. The attributes are configurable, and attribute strategies with hooks are available. The approach and concepts are inspired by the "Magento way". The application is specifically tailored for a custom CMS-oriented environment.

### Features
- single entity CRUD, validation
- import/export entities

### Requirements
- PHP >=8.1
- ext-pdo
- ext-bcmath
- Illuminate\validator ^9.0|^10.0
- Illuminate\translation ^9.0|^10.0

[Documentation](./docs/eav.md)

### Installation
```bash
composer require drobotik/eav
```

### Development
```bash
$ git clone git@github.com:drobotik/eav.git 
$ cd eav/docker 
# if docker
$ docker-compose up -d
$ docker-compose exec app bash
# 
$ composer install
# check cli app output
$ php eav 
```

### Planned features 

:heavy_check_mark: Domain import/export csv
<br>:pushpin: Attribute props and strategy improvements
<br>:pushpin: Going out from Laravel models, impl folder

### Contributing

Please note the following guidelines before submitting pull request.

- [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standards
- one pull request per feature
- implement your change and add tests for it
- ensure the test suite passes

### License

Eav package is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2023 [Aleksandr Drobotik](https://github.com/drobotik)