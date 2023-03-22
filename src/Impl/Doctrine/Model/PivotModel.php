<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Impl\Doctrine\Model;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Enum\_PIVOT;

class PivotModel
{
    private ?int $pivot_id = null;
    private ?int $domain_id = null;
    private ?int $set_id = null;
    private ?int $group_id = null;
    private ?int $attribute_id = null;

    public static function loadMetadata(ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable(_PIVOT::table());
        $builder->createField(_PIVOT::ID->column(), Types::INTEGER)
            ->makePrimaryKey()
            ->generatedValue()
            ->build();
        $builder->addField( _PIVOT::DOMAIN_ID->column(), Types::INTEGER);
        $builder->addField( _PIVOT::SET_ID->column(), Types::INTEGER);
        $builder->addField( _PIVOT::GROUP_ID->column(), Types::INTEGER);
        $builder->addField( _PIVOT::ATTR_ID->column(), Types::INTEGER);
    }


    public function getId() : ?int
    {
        return $this->pivot_id;
    }

    public function getDomainId(): ?int
    {
        return $this->domain_id;
    }

    public function setDomainId(int $id): self
    {
        $this->domain_id = $id;
        return $this;
    }

    public function getSetId(): ?int
    {
        return $this->set_id;
    }

    public function setSetId(int $id): self
    {
        $this->set_id = $id;
        return $this;
    }

    public function getGroupId(): ?int
    {
        return $this->group_id;
    }

    public function setGroupId(int $id): self
    {
        $this->group_id = $id;
        return $this;
    }

    public function getAttributeId(): ?int
    {
        return $this->attribute_id;
    }

    public function setAttributeId(int $id): self
    {
        $this->attribute_id = $id;
        return $this;
    }


}