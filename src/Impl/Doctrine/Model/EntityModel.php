<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Impl\Doctrine\Model;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Enum\_ENTITY;

class EntityModel
{
    private ?int $id = null;
    private ?int $domain_id = null;

    public static function loadMetadata(ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable(_ENTITY::table());
        $builder->createField(_ENTITY::ID->column(), 'integer')
            ->makePrimaryKey()
            ->generatedValue()
            ->build();
        $builder->addField( _ENTITY::DOMAIN_ID->column(), 'integer');
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getDomainId(): int
    {
        return $this->domain_id;
    }

    public function setDomainId(int $id): self
    {
        $this->domain_id = $id;
        return $this;
    }
}