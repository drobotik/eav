<?php

namespace Kuperwood\Eav\Result;

use Kuperwood\Eav\Transporter\Transporter;

class ModelResult
{
    public const NOT_PERFORMED = 0;
    public const COMPLETED = 1;
    public const REJECTED = 2;
    public const FAILED = 3;

    private int $status = self::NOT_PERFORMED;
    private ?string $message = null;

    private Transporter $transporter;

    public function setTransporter(Transporter $transporter) : self
    {
        $this->transporter = $transporter;
        return $this;
    }

    public function getTransporter() : Transporter
    {
        return $this->transporter;
    }

    public function __get(string $field)
    {
        return $this->transporter->getField($field);
    }

    public function setCompleted(string $message = "completed") : self
    {
        $this->status = self::COMPLETED;
        $this->setMessage($message);
        return $this;
    }

    public function setFailed(string $message) : self
    {
        $this->status = self::FAILED;
        $this->setMessage($message);
        return $this;
    }

    public function setStopped(string $message) : self
    {
        $this->status = self::REJECTED;
        $this->setMessage($message);
        return $this;
    }

    private function setMessage(string $message) : self
    {
        $this->message = $message;
        return $this;
    }

    public function setStatus(int $status) : self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus() : int
    {
        return $this->status;
    }

    public function getMessage() : ?string
    {
        return $this->message;
    }
}