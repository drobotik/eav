<?php

namespace Kuperwood\Eav\Result;

class Result
{
    public int $code;
    public string $message;

    public function code() : int
    {
        return $this->code;
    }

    public function message() : string
    {
        return $this->message;
    }
}