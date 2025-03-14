<?php

namespace Drobotik\Eav\Validation\Constraints;

use Drobotik\Eav\Interface\ConstraintInterface;

class DateConstraint implements ConstraintInterface
{
    private string $format;

    public function __construct(string $format = 'Y-m-d')
    {
        $this->format = $format;
    }

    public function validate($value): ?string
    {
        if (!is_string($value)) {
            return "This value must be a string.";
        }

        $dateTime = \DateTime::createFromFormat($this->format, $value);
        if (!$dateTime || $dateTime->format($this->format) !== $value) {
            return "This value must be a valid date in '{$this->format}' format.";
        }

        return null;
    }
}