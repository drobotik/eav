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

    private $data = null;

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

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    public function created(): self
    {
        return $this->setCode(_RESULT::CREATED)
            ->setMessage(_RESULT::message(_RESULT::CREATED));
    }

    public function updated(): self
    {
        return $this->setCode(_RESULT::UPDATED)
            ->setMessage(_RESULT::message(_RESULT::UPDATED));
    }

    public function deleted(): self
    {
        return $this->setCode(_RESULT::DELETED)
            ->setMessage(_RESULT::message(_RESULT::DELETED));
    }

    public function notDeleted(): self
    {
        return $this->setCode(_RESULT::NOT_DELETED)
            ->setMessage(_RESULT::message(_RESULT::NOT_DELETED));
    }

    public function found(): self
    {
        return $this->setCode(_RESULT::FOUND)
            ->setMessage(_RESULT::message(_RESULT::FOUND));
    }

    public function notFound(): self
    {
        return $this->setCode(_RESULT::NOT_FOUND)
            ->setMessage(_RESULT::message(_RESULT::NOT_FOUND));
    }

    public function notEnoughArgs(): self
    {
        return $this->setCode(_RESULT::NOT_ENOUGH_ARGS)
            ->setMessage(_RESULT::message(_RESULT::NOT_ENOUGH_ARGS));
    }

    public function notAllowed(): self
    {
        return $this->setCode(_RESULT::NOT_ALLOWED)
            ->setMessage(_RESULT::message(_RESULT::NOT_ALLOWED));
    }

    public function empty(): self
    {
        return $this->setCode(_RESULT::EMPTY)
            ->setMessage(_RESULT::message(_RESULT::EMPTY));
    }

    public function validationFails(): self
    {
        return $this->setCode(_RESULT::VALIDATION_FAILS)
            ->setMessage(_RESULT::message(_RESULT::VALIDATION_FAILS));
    }

    public function validationPassed(): self
    {
        return $this->setCode(_RESULT::VALIDATION_PASSED)
            ->setMessage(_RESULT::message(_RESULT::VALIDATION_PASSED));
    }

    public function exportSuccess(): self
    {
        return $this->setCode(_RESULT::EXPORT_SUCCESS)
            ->setMessage(_RESULT::message(_RESULT::EXPORT_SUCCESS));
    }

    public function exportFailed(): self
    {
        return $this->setCode(_RESULT::EXPORT_FAILED)
            ->setMessage(_RESULT::message(_RESULT::EXPORT_FAILED));
    }

    public function importSuccess(): self
    {
        return $this->setCode(_RESULT::IMPORT_SUCCESS)
            ->setMessage(_RESULT::message(_RESULT::IMPORT_SUCCESS));
    }

    public function importFailed(): self
    {
        return $this->setCode(_RESULT::IMPORT_FAILED)
            ->setMessage(_RESULT::message(_RESULT::IMPORT_FAILED));
    }
}
