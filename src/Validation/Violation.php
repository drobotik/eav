<?php

namespace Drobotik\Eav\Validation;

class Violation
{
    private string $message;
    private string $type;
    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
    }


    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPropertyPath(): string
    {
        return $this->type;
    }


}