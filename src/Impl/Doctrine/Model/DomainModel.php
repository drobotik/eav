<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Impl\Doctrine\Model;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Enum\_DOMAIN;

class DomainModel
{
    private ?int $domain_id = null;
    private string $name;

    public static function loadMetadata(ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable(_DOMAIN::table());
        $builder->createField(_DOMAIN::ID->column(), 'integer')
            ->makePrimaryKey()
            ->generatedValue()
            ->build();
        $builder->addField( _DOMAIN::NAME->column(), 'string');
    }

    public function getId() : int
    {
        return $this->domain_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }


}