<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

class Cleaner
{
    public static function run() {
        $paths = [
            __DIR__.'/temp/csv.csv'
        ];
        foreach ($paths as $path)
            if(file_exists($path))
                unlink($path);
    }
}