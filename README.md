# EAV library

[![Latest Version](https://img.shields.io/github/release/drobotik/eav.svg?style=flat-square)](https://github.com/drobotik/eav/releases)
[![Build Status](https://github.com/drobotik/eav/workflows/tests/badge.svg)](https://github.com/drobotik/eav/actions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://opensource.org/license/mit)
[![Quality Score](https://img.shields.io/scrutinizer/g/drobotik/eav.svg?style=flat-square)](https://scrutinizer-ci.com/g/drobotik/eav)
[![Code Coverage](https://scrutinizer-ci.com/g/drobotik/eav/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/drobotik/eav/?branch=master)

The EAV library is designed to manage and manipulate EAV data across multiple domains. Library offers functionalities for configurable entity CRUD, importing and exporting entities, as well as managing attribute sets and groups. The attributes are configurable, and attribute strategies with hooks are available. The approach and concepts are inspired by the "Magento way". The application is specifically tailored for a custom CMS-oriented environment.

### Features
- single entity CRUD, validation
~~- import/export entities~~

### Requirements
- PHP >=8.1
- ext-pdo
- ext-bcmath

[Documentation](./docs/eav.md)

### Installation
```bash
$ composer require drobotik/eav
```

### Planned features 

:heavy_check_mark: Domain import/export csv<br>
:heavy_check_mark: Switch to Symfony components<br>
:pushpin: Attribute props and strategy improvements

### License

Eav package is licensed under the [MIT License](http://opensource.org/licenses/MIT).

Copyright 2023 [Aleksandr Drobotik](https://github.com/drobotik)