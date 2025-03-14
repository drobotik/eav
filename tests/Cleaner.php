<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

use League\Csv\Writer;

class Cleaner
{
    public static function run() {
        self::resetCsv(__DIR__.'/Data/csv.csv');
    }

    private static function resetCsv(string $path): void
    {
//        $file = new \SplFileObject($path, 'w');
//        $writer = Writer::createFromFileObject($file);
//        $writer->insertAll([]);
    }

}