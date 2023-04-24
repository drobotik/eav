<?php
/**
 * This file is part of the eav package.
 *
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Result;

use Drobotik\Eav\Enum\_RESULT;

class Result
{
    private int $code;
    private string $message;

    private mixed $data = null;

    public function getCode(): int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function created(): self
    {
        return $this->setCode(_RESULT::CREATED->code())
            ->setMessage(_RESULT::CREATED->message());
    }

    public function updated(): self
    {
        return $this->setCode(_RESULT::UPDATED->code())
            ->setMessage(_RESULT::UPDATED->message());
    }

    public function deleted(): self
    {
        return $this->setCode(_RESULT::DELETED->code())
            ->setMessage(_RESULT::DELETED->message());
    }

    public function notDeleted(): self
    {
        return $this->setCode(_RESULT::NOT_DELETED->code())
            ->setMessage(_RESULT::NOT_DELETED->message());
    }

    public function found(): self
    {
        return $this->setCode(_RESULT::FOUND->code())
            ->setMessage(_RESULT::FOUND->message());
    }

    public function notFound(): self
    {
        return $this->setCode(_RESULT::NOT_FOUND->code())
            ->setMessage(_RESULT::NOT_FOUND->message());
    }

    public function notEnoughArgs(): self
    {
        return $this->setCode(_RESULT::NOT_ENOUGH_ARGS->code())
            ->setMessage(_RESULT::NOT_ENOUGH_ARGS->message());
    }

    public function notAllowed(): self
    {
        return $this->setCode(_RESULT::NOT_ALLOWED->code())
            ->setMessage(_RESULT::NOT_ALLOWED->message());
    }

    public function empty(): self
    {
        return $this->setCode(_RESULT::EMPTY->code())
            ->setMessage(_RESULT::EMPTY->message());
    }

    public function validationFails(): self
    {
        return $this->setCode(_RESULT::VALIDATION_FAILS->code())
            ->setMessage(_RESULT::VALIDATION_FAILS->message());
    }

    public function validationPassed(): self
    {
        return $this->setCode(_RESULT::VALIDATION_PASSED->code())
            ->setMessage(_RESULT::VALIDATION_PASSED->message());
    }

    public function exportSuccess(): self
    {
        return $this->setCode(_RESULT::EXPORT_SUCCESS->code())
            ->setMessage(_RESULT::EXPORT_SUCCESS->message());
    }

    public function exportFailed(): self
    {
        return $this->setCode(_RESULT::EXPORT_FAILED->code())
            ->setMessage(_RESULT::EXPORT_FAILED->message());
    }

    public function importSuccess(): self
    {
        return $this->setCode(_RESULT::IMPORT_SUCCESS->code())
            ->setMessage(_RESULT::IMPORT_SUCCESS->message());
    }

    public function importFailed(): self
    {
        return $this->setCode(_RESULT::IMPORT_FAILED->code())
            ->setMessage(_RESULT::IMPORT_FAILED->message());
    }
}
