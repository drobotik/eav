<?php

namespace Kuperwood\Eav\Validation\Constraints;

use Kuperwood\Eav\Interfaces\ConstraintInterface;

class NumericConstraint implements ConstraintInterface
{
    public function validate($value): ?string
    {
        if (!is_numeric($value)) {
            return "This value must be a number.";
        }
        return null;
    }
}