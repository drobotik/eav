<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Impl\Doctrine\Model;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Enum\_SET;

class SetModel
{
    private ?int $id = null;
    private ?string $name = null;

    public static function loadMetadata(ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable(_SET::table());
        $builder->createField(_SET::ID->column(), Types::INTEGER)
            ->makePrimaryKey()
            ->generatedValue()
            ->build();
        $builder->addField( _SET::NAME->column(), Types::STRING);
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}