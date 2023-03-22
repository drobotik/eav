<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Impl\Doctrine\Model;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Enum\AttributeTypeEnum;

class ValueIntegerModel extends ValueGenericModel
{
    private ?int $value;
    public static function loadMetadata(ClassMetadata $metadata)
    {
        AttributeTypeEnum::INTEGER->loadMetadata($metadata);
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;
        return $this;
    }
}