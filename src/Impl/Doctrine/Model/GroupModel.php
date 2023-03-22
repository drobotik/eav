<?php
declare(strict_types=1);
namespace Kuperwood\Eav\Impl\Doctrine\Model;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Enum\_GROUP;


class GroupModel
{

    private ?int $id = null;
    private ?int $set_id = null;
    private ?string $name = null;

    public static function loadMetadata(ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable(_GROUP::table());
        $builder->createField(_GROUP::ID->column(), Types::INTEGER)
            ->makePrimaryKey()
            ->generatedValue()
            ->build();
        $builder->addField( _GROUP::SET_ID->column(), Types::INTEGER);
        $builder->addField( _GROUP::NAME->column(), Types::STRING);
    }

    public function getId() : ?int
    {
        return $this->id;
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

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}