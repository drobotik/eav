<?php

namespace Drobotik\Eav\Validation\Constraints;

use Drobotik\Eav\Interface\ConstraintInterface;

class LengthConstraint implements ConstraintInterface
{
    private int $min;
    private int $max;

    public function __construct(int $min = 0, int $max = PHP_INT_MAX)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function validate($value): ?string
    {
        if (!is_string($value)) {
            return "This value must be a string.";
        }

        $length = strlen($value);
        if ($length < $this->min || $length > $this->max) {
            return "This value must be between {$this->min} and {$this->max} characters long.";
        }

        return null;
    }
}