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
For more details check enum class \Kuperwood\Eav\Enum\ATTR_TYPE<br>
Type used to determine the right data table:<br>
eav_value_int <br>
eav_value_varchar <br>
eav_value_datetime <br>
eav_value_decimal <br>
eav_value_text <br>

**strategy** - is a class name which will be used for CRUD operations with this attribute.<br>
If it's empty, default \Kuperwood\Eav\Strategy will be used.

**source** - 

**default_value** - this value will be used if there no manual value provided

**description** - attribute description
<hr>

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
And the last step is to create a relation records to associate attributes to the attribute set and groups
```php
$stmt = $pdo->prepare("INSERT INTO eav_pivot (domain_id, set_id, group_id, attribute_id) VALUE (1,1,1,1)");
$stmt->execute();
$stmt = $pdo->prepare("INSERT INTO eav_pivot (domain_id, set_id, group_id, attribute_id) VALUE (1,1,2,2)");
$stmt->execute();
```

EAV management is finished. 

### Entity CRUD

When EAV management is done, CRUD engine is ready to go.

```php
use Kuperwood\Eav\Entity;
use Kuperwood\Eav\Enum\_RESULT;

$model = new Entity();
$model->setDomainKey(1);
$model->getAttributeSet()->setKey(1);
$attributeBag = $model->getBag();
$attributeBag->setField('name', 'Notebook 1'); // will store to eav_value_varchar
$attributeBag->setField('ram', 1024); // will store to eav_value_int
$model->save();

// Find entity by entity id
$record = Entity::findByKey(1)
$data = $record->toArray()
$attributeBag = $model->getBag();
$attributeBag->setField('name', 'Other');

$validation = $record->validate();
if ($validation->getCode() === _RESULT::VALIDATION_FAILS) {
    var_dump($validation->getData()); exit();
}
$result = $record->save();
```


