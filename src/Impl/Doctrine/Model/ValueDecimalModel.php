<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Impl\Doctrine\Model;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Enum\AttributeTypeEnum;

class ValueDecimalModel extends ValueGenericModel
{
    private ?float $value;
    public static function loadMetadata(ClassMetadata $metadata)
    {
        AttributeTypeEnum::DECIMAL->loadMetadata($metadata);
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }
}