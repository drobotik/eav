<?php
/**
 * This file is part of the eav package.
 * @author    Alex Kuperwood <alexkuperwood@gmail.com>
 * @copyright 2025 Alex Kuperwood
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Kuperwood\Eav\Interfaces;

use Kuperwood\Eav\Result\Result;

interface StrategyInterface
{
    public function validate(): Result;
    public function find() : Result;
    public function save() : Result;
    public function delete() : Result;

}