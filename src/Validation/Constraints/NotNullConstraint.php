<?php

namespace Kuperwood\Eav\Validation\Constraints;

use Kuperwood\Eav\Interfaces\ConstraintInterface;

class NotNullConstraint implements ConstraintInterface
{
    public function validate($value): ?string
    {
        if ($value === null) {
            return "This value cannot be null.";
        }
        return null;
    }
}
