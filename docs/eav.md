
### Table of Contents
- Common
    - <a href="#cli-app">CLI</a>
    - <a href="#connection">Connection</a>
    - <a href="#laravel-connection">Laravel connection</a>
    - <a href="#dbal-migrations">DBAL migrations</a>
    - <a href="#laravel-migrations">Support Laravel migrations</a>
    - <a href="#laravel-models">Laravel models</a>
- <a href="#objects">Objects</a>
    - <a href="#domain">Domain</a>
    - <a href="#entity">Entity</a>
    - <a href="#attribute-set">Attribute set</a>
    - <a href="#attribute-group">Attribute group</a>
    - <a href="#attribute">Attribute</a>
    - <a href="#attribute-container">Attribute container</a>
    - <a href="#attribute-strategy">Attribute strategy</a>
    - <a href="#attribute-set-action">Attribute set action</a>
    - <a href="#value">Value</a>
    - <a href="#pivot">Pivot</a>
- <a href="#import">Import</a>
    - <a href="#import-setup">Setup</a>
    - <a href="#import-attributes">Importing new attributes</a>
    - <a href="#import-data">Importing data</a>
- <a href="#export">Export</a>
    - <a href="#export-setup">Setup</a>
    - <a href="#export-qeerying-data">Querying data</a>
- <a href="#factory">Factory</a>
    - <a href="#eav-factory">Eav factory</a>
    - <a href="#entity-factory">Entity factory</a>
- <a href="#presenters">Examples</a>

### CLI app
The Symfony app utilizes DBAL migration commands. 
The CLI app is primarily used for executing migrations.

### Connection
The CLI app relies on a database connection and utilizes Doctrine DBAL for establishing connections.<br />
No pre-configured connection information is available. To set up a connection in non-docker environments, you need to follow these steps:

1. Create a new file named connection.php in the root folder.
2. The contents of this file will be automatically included in the application.
```php
#connection.php
use Drobotik\Eav\Database\Connection;
$config = [
    // connection settings
]
$connection = Connection::get($config)
```
Choose a [driver](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#driver) that best suits your requirements.
### Laravel connection
The program currently utilizes Laravel models, which means it uses Laravel Capsule for database connections. By default, the program will use the default connection within a Laravel application. However, for other cases, you need to initialize a Capsule instance.
For the CLI app, you can add the following code to the connections.php file:
```php
$capsule = new Capsule;
$capsule->addConnection([
    // connection settings
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
```

### DBAL migrations
Migrate:
```bash
$ php eav migrations:migrate latest
```
Rollback:
```bash
$ php eav migrations:migrate first
```
### Laravel migrations
Publishing on Laravel app. 
Add EavServiceProvider to app config.
```php
\Drobotik\Eav\Database\Support\Laravel\EavServiceProvider::class
```
Publish migrations by vendor:publish.
### Laravel models

Currently, the library is dependent on Laravel's Illuminate\Database and utilizes Laravel models for the following table structure:

- eav_domains
- eav_entities
- eav_attribute_sets
- eav_attribute_groups
- eav_attributes
- eav_pivot
- eav_value_string
- eav_value_integer
- eav_value_decimal
- eav_value_datetime
- eav_value_text

These Laravel models are used to interact with these tables and perform various database operations.

## Objects

### Domain

The Domain is used solely as a wrapper for performing import and export operations. It provides a convenient interface for managing the import/export functionality within the library.
```php
$domain = new \Drobotik\Eav\Domain();
$domain->setExportManager(new \Drobotik\Eav\Export\ExportManager());
$domain->setImportManager(new \Drobotik\Eav\Export\ImportManager());

$exportManager = $domain->getExportManager();
$importManager = $domain->getImportManager();

$domain->import();
$domain->export();
```

### Entity

The Entity model is a robust CRUD model that simplifies working with the EAV (Entity-Attribute-Value) data structure. It serves as the primary model for working with individual data records, making it well-suited for single record operations. However, it is not recommended for bulk usage or performing operations on a large number of records simultaneously.

```php

$entityKey = 1;
$domainKey = 2;
$attributeSetKey = 3;

$entity = new \Drobotik\Eav\Entity();

// SETUP
$entity->setDomainKey($entityKey);
$entity->setKey($domainKey);
$entity->getAttributeSet()->setKey($attributeSetKey)

// FIND
$result = $entity->find();
$data = $result->getData();

$fields = $entity->getBag();
// EDIT record attributes
$fields->setField('name', 'Tom');
$fields->setField('type', 'Jerry');
// remove record attribute value
$fields->removeField('age');
// it will be ignored since there are no attributes in the attribute set.
$fields->setField('not_existing_attribute', '');

// VALIDATE
// validate attributes and values
$result = $entity->validate(); // result from laravel validator 
// override your translator if needed

$result->getData(); // validation array
$result->getCode(); // just for internal use
$result->getMessage(); // human message

// SAVE
// attribute strategy before save hooks
// values will be inserted/updated or deleted
// attribute strategy after save hooks
$entity->save();

// DELETE
// entity with corresponded values will be deleted
$entity->delete();

```
Entity create (without $entity->setKey())
```php
$domainKey = 2;
$attributeSetKey = 3;

$entity = new \Drobotik\Eav\Entity();

// SETUP
$entity->setDomainKey($entityKey);
$entity->getAttributeSet()->setKey($attributeSetKey);
$fields = $entity->getBag();
$fields->setField('name', 'Tom');
$fields->setField('type', 'Cat');

$entity->save();
```
Other features
```php
    /* Override attributeSet */
    $entity = new \Drobotik\Eav\Entity();
    $attrSet = new \Drobotik\Eav\AttributeSet();
    $entity->setAttributeSet($attrSet);
    
    /* CRUD gnome doing all routine jobs for 'Entity' */
    $gnome = $entity->getGnome();
    
    /* toArray */
    $entity->toArray(); 
```
More examples:<br>
tests/Eav/Entity/[EntityAcceptanceTest.php](https://github.com/drobotik/eav/blob/master/tests/Eav/Entity/EntityAcceptanceTest.php)

### Attribute set

This class serves as a wrapper for Attribute Containers and is an integral part of Entity.
```php
$attributeSetKey = 1;

$set = new \Drobotik\Eav\AttributeSet();
$set->setKey($attributeSetKey);
/* 
 * Making new db query to get corresponded attributes;
 * Initializes this Attribute containers
 * Initializes Attribute, Strategy, Value and other objects
 * Grav Values from database if they exist
 */
$set->fetchContainers();

/* 
 * It can be forced to rebuild all Attribute containers array from the scratch
 */
$set->fetchContainers(true);

/* 
 * Other methods
 */
$set->hasContainer('test');
$set->getContainer('test');
$set->pushContainer(new \Drobotik\Eav\AttributeContainer());
$set->getContainers(); // \Drobotik\Eav\AttributeContainer[] keyBy name
$set->resetContainers(); // []
$set->setEntity(new \Drobotik\Eav\Entity());
$set->getEntity(); // \Drobotik\Eav\Entity
```

### Attribute group

Attribute groups are utilized to categorize attributes into subsections. It is important to note that without a group, an attribute set is unable to fetch containers.
<br>Please see <a href="#pivot">Pivot</a>.

### Attribute

The Attribute class represents attribute data retrieved from the database. It plays a crucial role in managing how data should be saved and determining the source of initial data. An important component of the Attribute class is the AttributeStrategy class, which handles all CRUD operations related to attribute values.

```php

$attribute = new \Drobotik\Eav\Attribute();
$bag = $attribute->getBag();

$bag->setField(\Drobotik\Eav\Enum\_ATTR::NAME->column(), 'price')
$bag->setField(\Drobotik\Eav\Enum\_ATTR::TYPE->column(), \Drobotik\Eav\Enum\ATTR_TYPE::DECIMAL->value())
$bag->setField(\Drobotik\Eav\Enum\_ATTR::STRATEGY->column(), \Drobotik\Eav\Strategy::class);


$type = $attribute->getType(); // \Drobotik\Eav\Enum\ATTR_TYPE::DECIMAL
$valueModel = $type->model(); // \Drobotik\Eav\Enum\ATTR_TYPE::DECIMAL->model()
$valueModelTable = $type->valueTable(); // \Drobotik\Eav\Enum\ATTR_TYPE::DECIMAL->valueTable()
$typeName = $type->value(); // \Drobotik\Eav\Enum\ATTR_TYPE::DECIMAL->value()
// ...

$attribute->getName();
$attribute->getStrategy();
$attribute->getSource();
$attribute->getDefaultValue();
$attribute->getDescription();

```

### Attribute container

The AttributeContainer class is extensively utilized by the AttributeSet to construct and store attribute containers for each attribute in the AttributeSet.

```php

$container = new \Drobotik\Eav\AttributeContainer();

$container->setAttributeSet(new \Drobotik\Eav\AttributeSet());
$container->getAttributeSet();
$container->setAttribute(new \Drobotik\Eav\Attribute());
$container->getAttribute();
$container->setAttributeSetAction(new \Drobotik\Eav\AttributeSetAction());
$container->getAttributeSetAction();
$container->setStrategy(new \Drobotik\Eav\Strategy());
$container->getStrategy();
$container->setValueAction(new \Drobotik\Eav\Value\ValueAction());
$container->getValueAction();
$container->setValueValidator(new \Drobotik\Eav\Value\ValueValidator());
$container->getValueValidator();
$container->setValueManager(new \Drobotik\Eav\Value\ValueManager());
$container->getValueManager();

```

### Attribute strategy

The strategy class is frequently employed to customize manipulations with specific attributes or to introduce new features. The current strategy includes predefined CRUD operations and validation for Attribute EAV values.

```php

$strategy = new \Drobotik\Eav\Strategy();

$strategy->delete();
$strategy->create();
$strategy->delete();
$strategy->validate();
$strategy->save();
$strategy->find()

$strategy->beforeCreate();
$strategy->beforeUpdate();
$strategy->beforeDelete();
$strategy->afterCreate();
$strategy->afterUpdate();
$strategy->afterDelete();

$strategy->rules(); // get validation rules
$strategy->isCreate();
$strategy->isUpdate();

```

### Attribute set action

The helper class is utilized to initialize attribute objects. The AttributeSet employs this action when fetching attributes.

```php

$action = new \Drobotik\Eav\AttributeSetAction();
$action->setAttributeContainer(new \Drobotik\Eav\AttributeContainer());

$record = new \Drobotik\Eav\Model\AttributeModel();
$record->setName('test');

$action->initialize($record);

// more internal methods
$action->initializeStrategy($record); 
$action->initializeValueManager();

```

### Value

The Value class is an internal object used to handle EAV (Entity-Attribute-Value) values. It can store two types of values: runtimeValue and storedValue.

The runtimeValue represents a dynamic value that is intended to be saved in the database but has not been stored yet.

On the other hand, the storedValue represents a value that has already been stored in the database.

```php

$value = new \Drobotik\Eav\Value\ValueManager();
$value->setStored(123);
$value->getValue(); // 123
$value->setRuntime(456); 
$value->getValue(); // 456
$value->IsEquivalent() // false
$value->isRuntime(); // true
$value->clearRuntime(); 
$value->isRuntime(); // false
$value->isStored(); // true

```

### Pivot

Before working with EAV (Entity-Attribute-Value) data, it is necessary to specify the structure of the attribute set. If an attribute is not linked in the pivot table, both the attribute and its corresponding data will not be fetched by the AttributeSet.

```php
$model = new \Drobotik\Eav\Model\PivotModel();
$model->setDomainKey(1);
$model->setAttrSetKey(2);
$model->setGroupKey(3);
$model->setAttrKey(4);
$model->save();
```

### Import

Attribute import:
During the import process, the attribute import functionality creates new attributes that are linked to the attribute set. These attributes are specifically added and associated with the attribute set.

Data Import:
The Data Import feature is responsible for importing values for attributes that belong to the attribute set. It handles the import of actual data associated with the attributes.

Before starting the program, the data is divided into two parts: new entities to create and entities to update.

New entities to create:
For new entities, a bulk import query is used, which offers fast processing. However, it is recommended to avoid using a large chunk size to prevent the query from becoming too large.

Entities to update:
The program further divides entities to update into three categories: new values, values to update, and values to create.

### Import data

```php
// Import required a source. In this example source is csv file.
$file = new \SplFileObject('dir/data.csv', 'r');
$reader = Reader::createFromFileObject($file);
$reader->setDelimiter(',');
$reader->setHeaderOffset(0);

$driver = new CsvDriver();
// index or line number where to start from
$driver->setCursor(0);
// import data going to be imported by chunked, after each iteration default state restored.
$driver->setChunkSize(50); // recommended to not use large chunks   
$driver->setReader($reader);

// this worker will insert data to database
$contentWorker = new \Drobotik\Eav\Import\Content\Worker();
// Import DI container
$importContainer = new \Drobotik\Eav\Import\ImportContainer();
$importContainer->setContentWorker($contentWorker);

// main handler that will make everything work on run()
$importManager = new \Drobotik\Eav\Import\ImportManager();
$importManager->setContainer($importContainer);

```

### Import attributes
During the import process, the AttributesWorker component will retrieve column names from the source data. These column names will then be validated. If any of the column names do not correspond to existing attributes (new attributes) or attributes that are not linked with the attribute set, the program will raise an exception and provide the names of the attributes that need to be addressed.

To ensure the import process works correctly, a configuration for these attributes needs to be provided. This configuration will facilitate the creation of the necessary attributes before proceeding with the data import.
```php
$groupKey = 1;

$ageAttribute  = new \Drobotik\Eav\Import\Attributes\ConfigAttribute()
$ageAttribute->setFields([
    _ATTR::NAME->column() => "age",
    _ATTR::TYPE->column() => ATTR_TYPE::INT->value()
]);
$ageAttribute->setGroupKey($groupKey);

$noteAttribute  =  new \Drobotik\Eav\Import\Attributes\ConfigAttribute()
$noteAttribute->setFields([
    _ATTR::NAME->column() => "note",
    _ATTR::TYPE->column() => ATTR_TYPE::TEXT->value()
]);
$noteAttribute->setGroupKey($groupKey);

$config = new \Drobotik\Eav\Import\Attributes\Config();
$config->appendAttribute($ageAttribute);
$config->appendAttribute($noteAttribute);

$attributesWorker = new \Drobotik\Eav\Import\Attributes\Worker();
$attributesWorker->setConfig($config);

/** @var \Drobotik\Eav\Import\ImportContainer $importContainer */
$importContainer->setAttributesWorker($attributesWorker);
```

See more comprehensive examples in the
tests/Eav/ImportManager/[ImportManagerAcceptanceTest.php](https://github.com/drobotik/eav/blob/master/tests/Eav/ImportManager/ImportManagerAcceptanceTest.php)

### Export
The export mechanism is divided into two sections: setup and querying data.

The setup section involves configuring the driver and main classes. In this example, the CsvDriver setup is used.

The querying data section involves utilizing an internal query builder within the program. The idea for the query builder originates from [jquery query builder](https://querybuilder.js.org/).

The query builder generates a custom query based on the provided configuration.

```php
// specify where to write
$file = new \SplFileObject(d'/dir/data.csv','w');
$writer = Writer::createFromFileObject($file);
$driver = new \Drobotik\Eav\Driver\CsvDriver();
$driver->setWriter($writer);

$manager = new \Drobotik\Eav\Export\ExportManager();
$manager->setDriver($driver);

// query builder config
$config = [
    QB_CONFIG::CONDITION => QB_CONDITION::AND,
    QB_CONFIG::RULES => [
        [
            QB_CONFIG::NAME => "size",
            QB_CONFIG::OPERATOR => QB_OPERATOR::LESS->name(),
            QB_CONFIG::VALUE => 10000
        ], // size < 10000
        [ // size < 10000 and ( name like '%sit quisquam%' or name = 'et dolores'
            QB_CONFIG::CONDITION => QB_CONDITION::OR,
            QB_CONFIG::RULES => [
                [
                    QB_CONFIG::NAME => "name",
                    QB_CONFIG::OPERATOR => QB_OPERATOR::CONTAINS->name(),
                    QB_CONFIG::VALUE => 'sit quisquam'
                ],
                [
                    QB_CONFIG::NAME => "name",
                    QB_CONFIG::OPERATOR => QB_OPERATOR::EQUAL->name(),
                    QB_CONFIG::VALUE => 'et dolores'
                ]
            ],
        ]
    ],
];

$domainKey = 1;
$setKey = 2;

$qbManager = new \Drobotik\Eav\QueryBuilder\QueryBuilderManager();
$qbManager->setDomainKey($domainKey);
$qbManager->setSetKey($setKey);
$qbManager->setFilters($config);
// specify columns that should be on dataset. Only existing attributes linked to attribute set 
$qbManager->setColumns(["name", "size", "color"]);

$manager->setQueryBuilderManager($qbManager);

$manager->run(); // file created

```

### Factory

Sometimes, it may be necessary to pre-generate certain EAV data in advance.

### Eav factory

This is a collection of common 'create' methods for various entities such as domain, attribute, group, entity, values. These methods are extensively utilized during application testing, but they can also be employed during runtime if required.

### Entity factory

The entity factory is a configurable entity creation tool. By providing it with the necessary attribute configuration, it can generate an entity with all the corresponding attributes and values. This factory is primarily used for testing purposes. However, it should be noted that performance may be slow when dealing with a large number of entities.