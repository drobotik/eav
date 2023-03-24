<?php

namespace Kuperwood\Eav\Enum;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kuperwood\Eav\Model\ValueBase;
use Kuperwood\Eav\Model\ValueDatetimeModel;
use Kuperwood\Eav\Model\ValueDecimalModel;
use Kuperwood\Eav\Model\ValueIntegerModel;
use Kuperwood\Eav\Model\ValueStringModel;
use Kuperwood\Eav\Model\ValueTextModel;

enum ATTR_TYPE
{
    case INTEGER;
    case DATETIME;
    case DECIMAL;
    case STRING;
    case TEXT;
    case MANUAL;

    public function value(): string
    {
        return match ($this) {
            self::INTEGER => "int",
            self::DATETIME => "datetime",
            self::DECIMAL => "decimal",
            self::STRING => "varchar",
            self::TEXT => "text",
            self::MANUAL => "manual"
        };
    }

    public function valueTable(): string
    {
        return match ($this) {
            self::INTEGER => sprintf(_VALUE::table(), self::INTEGER->value()),
            self::DATETIME => sprintf(_VALUE::table(), self::DATETIME->value()),
            self::DECIMAL => sprintf(_VALUE::table(), self::DECIMAL->value()),
            self::STRING => sprintf(_VALUE::table(), self::STRING->value()),
            self::TEXT => sprintf(_VALUE::table(), self::TEXT->value())
        };
    }

    public function model(): ValueBase
    {
        return match ($this) {
            self::INTEGER => new ValueIntegerModel,
            self::DATETIME => new ValueDatetimeModel,
            self::DECIMAL => new ValueDecimalModel,
            self::STRING => new ValueStringModel,
            self::TEXT => new ValueTextModel
        };
    }

    public function doctrineType() : string
    {
        return match ($this) {
            self::DATETIME => Types::DATETIME_MUTABLE,
            self::DECIMAL => Types::DECIMAL,
            self::INTEGER => Types::INTEGER,
            self::TEXT => Types::TEXT,
            self::STRING => Types::STRING,
        };
    }

    public function loadMetadata(ClassMetadata $metadata) {

        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable(sprintf(_VALUE::table(), $this->value()));
        $builder->createField(_VALUE::ID->column(), Types::INTEGER)
            ->makePrimaryKey()
            ->generatedValue()
            ->build();
        $builder->addField( _VALUE::DOMAIN_ID->column(), Types::INTEGER);
        $builder->addField( _VALUE::ENTITY_ID->column(), Types::INTEGER);
        $builder->addField( _VALUE::ATTRIBUTE_ID->column(), Types::INTEGER);
        $builder->addField( _VALUE::VALUE->column(), $this->doctrineType());
    }

}
