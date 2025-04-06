<?php

namespace Kuperwood\Eav\Validation\Constraints;

use Kuperwood\Eav\Interfaces\ConstraintInterface;

class RegexConstraint implements ConstraintInterface
{
    private string $pattern;
    private string $message;

    public function __construct(string $pattern, string $message = "This value does not match the required pattern.")
    {
        $this->pattern = $pattern;
        $this->message = $message;
    }

    public function validate($value): ?string
    {
        if (!preg_match($this->pattern, $value)) {
            return $this->message;
        }

        return null;
    }
}