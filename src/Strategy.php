<?php

declare(strict_types=1);

namespace Kuperwood\Eav;

use Kuperwood\Eav\Enum\VALUE_RESULT;
use Kuperwood\Eav\Interface\StrategyInterface;
use Kuperwood\Eav\Result\ValueResult;

class Strategy implements StrategyInterface
{
    private Attribute    $attribute;
    private ValueManager $valueManager;

    public function getAttribute() : Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(Attribute $attribute) : self
    {
        $this->attribute = $attribute;
        return $this;
    }
    
    public function getValueManager() : ValueManager
    {
        return $this->valueManager;
    }

    public function setValueManager(ValueManager $value) : self
    {
        $this->valueManager = $value;
        return $this;
    }

    public function createValue() : ValueResult
    {
        $result = new ValueResult();

        $attribute = $this->getAttribute();
        $set = $attribute->getAttributeSet();
        $entity = $set->getEntity();
        $value = $this->getValueManager();
        $model = $attribute->getValueModel();

        if(!$value->isRuntime()) {
            $result->setCode(VALUE_RESULT::EMPTY->code())
                ->setMessage(VALUE_RESULT::EMPTY->message());
            return $result;
        }

        $model->setDomainKey($entity->getDomainKey())
            ->setEntityKey($entity->getKey())
            ->setAttrKey($attribute->getKey())
            ->setVal($value->getRuntime())
            ->save();

        $model->refresh();
        $value->setStored($model->getVal())
            ->setKey($model->getKey())
            ->clearRuntime();

        $result->setCode(VALUE_RESULT::CREATED->code())
            ->setMessage(VALUE_RESULT::CREATED->message());

        return $result;
    }


    public function rules(): array
    {
        // TODO: Implement rules() method.
    }

    public function validate(): false|array
    {
        // TODO: Implement validate() method.
    }

    public function find(): ValueResult
    {
        // TODO: Implement find() method.
    }

    public function save(string $type): ValueResult
    {
        // TODO: Implement save() method.
    }

    public function destroy(): ValueResult
    {
        // TODO: Implement destroy() method.
    }
}