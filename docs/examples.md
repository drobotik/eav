### EAV management
How it looks like with simple sql statements.
First, need to create domain record. Domain represents the structure. For example products
```php
$pdo = new PDO()
$stmt = $pdo->prepare("INSERT INTO eav_domains (name) VALUE ('Products')");
$stmt->execute();
```
Then let's create first attribute set for that domain. For example notebook.
```php
$stmt = $pdo->prepare("INSERT INTO eav_attribute_sets (domain_id, name) VALUE (1, 'Notebooks')");
$stmt->execute();
```
Attribute set must have at least one group of attributes. But usually more. 
```php
$stmt = $pdo->prepare("INSERT INTO eav_attribute_groups (set_id, name) VALUE (1, 'Main')");
$stmt->execute();

$stmt = $pdo->prepare("INSERT INTO eav_attribute_groups (set_id, name) VALUE (1, 'Specifications')");
$stmt->execute();
```
Now we have domain_id, and set_id and it's possible to create first domain entity
```php
$stmt = $pdo->prepare("INSERT INTO eav_entities (domain_id, set_id) VALUE (1, 1)");
$stmt->execute();
```
Now we have products data domain with one entity, and attribute set for notebooks which have two attribute groups: main and specifications.

It's time to manage attributes.
Attribute record description: <br>
**domain_id** - damain ID <br>
**name** - attribute name <br>
**type** - attribute type, it can be: int, varchar, datetime, decimal or text. <br>
For more details check enum class \Kuperwood\Eav\Enum\ATTR_TYPE
**strategy** - is a class name which will be used for CRUD operations with this attribute. If it's empty, default \Kuperwood\Eav\Strategy will be used.
**source** - 
**default_value** - this value will be used if there no manual value provided
**description** - attribute description
It's time to create attributes for that attribute_set groups.
```php
## Name attribute
$stmt = $pdo->prepare("INSERT INTO eav_attributes (
    domain_id, 
    name, 
    type, 
    strategy, 
    source, 
    default_value, 
    description
    ) VALUES (1, 'name', 'varchar', '', '', '', 'Notebook name')");
$stmt->execute();
## RAM attribute
$stmt = $pdo->prepare("INSERT INTO eav_attributes (
    domain_id, 
    name, 
    type, 
    strategy, 
    source, 
    default_value, 
    description
    ) VALUES (1, 'ram', 'int', '', '', '', 'Notebook RAM')");
$stmt->execute();
```
On that point. EAV management is finished. It's possible to CRUD entity data using eav engine.
```php
use Kuperwood\Eav\Entity;

$model = new Entity();
$model->setDomainKey(1);
$model->getAttributeSet()->setKey(1);
$attributeBag = $model->getBag();
$attributeBag->setField('name', 'Notebook 1');
$attributeBag->setField('ram', 1024);
$model->save();

// Find entity by entity id
$record = Entity::findByKey(1)
$data = $record->toArray()
```
