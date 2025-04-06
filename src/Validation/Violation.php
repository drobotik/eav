<?php

namespace Kuperwood\Eav\Validation;

class Violation
{
    private string $message;
    private string $field;
    public function __construct($field, $message)
    {
        $this->field = $field;
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getField(): string
    {
        return $this->field;
    }
}