<?php

namespace Drobotik\Eav\Validation;

use Drobotik\Eav\Interfaces\ConstraintInterface;
use Drobotik\Eav\Validation\Constraints\RequiredConstraint;

class Validator
{

    /**
     * @param $value
     * @param array $constraints
     * @return Violation[]
     */
    public function validate($field, $value, array $constraints)
    {
        foreach ($constraints as $constraint) {
            if ($constraint instanceof ConstraintInterface) {
                $violation = $constraint->validate($value);
                if ($violation !== null) {
                    return new Violation($field, $violation);
                }
            }
        }

        return null;
    }

    public function validateAll(array $data, array $rules): array
    {
        $violations = [];

        foreach ($rules as $field => $constraints) {
            $isRequired = false;

            // Check if RequiredConstraint is present
            foreach ($constraints as $constraint) {
                if ($constraint instanceof RequiredConstraint) {
                    $isRequired = true;
                    break;
                }
            }

            // If required but missing, add an error
            if ($isRequired && !isset($data[$field])) {
                $violations[] = new Violation($field, "The field '$field' is required.");
                continue;
            }

            // If the field is not required and missing, skip validation
            if (!$isRequired && !isset($data[$field])) {
                continue;
            }

            // Validate the field if present
            $violation = $this->validate($field, $data[$field], $constraints);
            if (!empty($violation)) {
                $violations[] = $violation;
            }
        }

        return $violations;
    }
}