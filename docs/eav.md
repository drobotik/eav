
### Table of Contents
- Common
    - <a href="#cli-app">CLI</a>
    - Connections
      - <a href="#cli-connection">CLI app overriding connection</a>
      - <a href="#cli-connection">Laravel applications connection</a>
    - <a href="#dbal-migrations">DBAL migrations</a>
    - <a href="#laravel-migrations">Support Laravel migrations</a>
    - <a href="#laravel-models">Laravel models</a>
- <a href="#objects">Objects</a>
    - <a href="#domain">Domain</a>
    - <a href="#entity">Entity</a>
    - <a href="#attribute-set">Attribute set</a>
    - <a href="#attribute-group">AttributeGroup</a>
    - <a href="#attribute">Attribute</a>
    - <a href="#attribute-container">Attribute container</a>
    - <a href="#attribute-set-action">AttributeSetAction</a>
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
Symphony app with DBAL migrations commands. 
This also can contain Doctrine entities, but was disabled

### CLI connection
CLI app relies on a database connection. It uses [Doctrine DBAL](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#configuration) to perform connections.<br />
There isn't any pre-configured connection information. For non-docker environments is required to set up a connection.
As an option, make a new file connection.php in root folder. This file will be automatically included.
```php
#connection.php
use Drobotik\Eav\Database\Connection;
$config = [
    // connection settings
]
$connection = Connection::get($config)
```
Note use a [driver](https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#driver) that suits your needs.

### Laravel connection
Library uses Laravel Capsule for connections.
Within Laravel application default connection will be used.
For other cases, initialize a Capsule instance:
```php
$capsule = new Capsule;
$capsule->addConnection([
    // connection settings
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();
```

### DBAL migrations
Just use Doctrine migrations.
Migrate to the end:
```bash
$ php eav migrations:migrate latest
```
Rollback:
```bash
$ php eav migrations:migrate first
```
### Laravel migrations
Publishing on Laravel app. Add EavServiceProvider to
app config.
```php
\Drobotik\Eav\Database\Support\Laravel\EavServiceProvider::class
```
Publish migrations by vendor:publish.

### Laravel models

At this moment library dependent from Illuminate\database Laravel models, they are used for this structure tables:
domain, entity, attribute_set, attribute_group, attribute, pivot,
string_value, integer_value, decimal_value, datetime_value, text_value.

## Objects

### Domain
Domain is used just as wrapper to perform import/export.
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
Entity is a robust CRUD model. 
Used to simplify routine with EAV data structure. 
Main model to work with single data.
Good for single record operations. 

Not recommended for bulk usage.

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
// will be ignored since there no attribute in attribute set
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
To create new entity. New instance without $entity->setKey().
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
More examples:
tests/Eav/Entity/EntityAcceptanceTest.php

### Attribute set

This class is a wrapper for Attribute Containers.
Entity aggregated composition. Part of Entity.
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

Attribute group is used to separate attributes for subsections. 
Without group Attribute set not able to fetchContainers.
Please see <a href="#pivot">Pivot</a>.

### Attribute

Attribute is a wrapper for attribute data from database

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

Dependency injection class. Attribute set constructing attribute containers 
for each attribute.

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

### Attribute set action

Helper class that is used to initialize attribute objects. 
Attribute set used this action on fetchAttributes

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

Value is internal object which is used to work with EAV value. 
It can store two types of value, - runtimeValue and storedValue.
Runtime value - some dynamic value claiming to be saved in database
Stored value - value that is already stored in database

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

#### Pivot

Before to work with EAV data, need to specify structure of attribute set.
If attribute is not linked in pivot table, the attribute & data will be not fetched.

```php
$model = new \Drobotik\Eav\Model\PivotModel();
$model->setDomainKey(1);
$model->setAttrSetKey(2);
$model->setGroupKey(3);
$model->setAttrKey(4);
$model->save();
```

### Import

#### Import setup

#### Import attributes

#### Import data

### Export

#### Export setup

#### Export querying data

### Factory

#### Eav factory

#### Entity factory



