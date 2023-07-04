<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav\Validation;

use Symfony\Component\Validator\Constraints;
class Assert
{
    public static function integer(): Constraints\IsTrue
    {
        return new Constraints\IsTrue([
            'message' => 'Should be an integer',
            'payload' => function ($value) {
                return is_numeric($value) && (int)$value == $value;
            }
        ]);
    }
}