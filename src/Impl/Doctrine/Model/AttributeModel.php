<?php

declare(strict_types=1);

namespace Kuperwood\Eav\Impl\Doctrine\Model;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Enum\_ATTR;


class AttributeModel
{

    private ?int $id = null;
    private int $domain_id;
    private string $name;
    private string $type;
    private ?string $strategy = null;
    private ?string $source = null;
    private ?string $default_value = null;
    private ?string $description = null;

    public static function loadMetadata(ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable(_ATTR::table());
        $builder->createField(_ATTR::ID->column(), Types::INTEGER)
            ->makePrimaryKey()
            ->generatedValue()
            ->build();
        $builder->addField(_ATTR::DOMAIN_ID->column(), Types::INTEGER);
        $builder->addField(_ATTR::NAME->column(), Types::STRING);
        $builder->addField(_ATTR::TYPE->column(), Types::STRING);
        $builder->addField(_ATTR::STRATEGY->column(), Types::STRING);
        $builder->addField(_ATTR::SOURCE->column(), Types::STRING);
        $builder->addField(_ATTR::DEFAULT_VALUE->column(), Types::STRING);
        $builder->addField(_ATTR::DESCRIPTION->column(), Types::STRING);
    }


    public function getId() : ?int
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getStrategy(): ?string
    {
        return $this->strategy;
    }

    public function setStrategy(string $strategy): self
    {
        $this->strategy = $strategy;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getDefaultValue(): ?string
    {
        return $this->default_value;
    }

    public function setDefaultValue(string $defaultValue): self
    {
        $this->default_value = $defaultValue;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
}