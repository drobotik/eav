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