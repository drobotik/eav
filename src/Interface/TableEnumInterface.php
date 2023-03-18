<?php

namespace Kuperwood\Eav\Interface;

interface TableEnumInterface
{
    public static function table() : string;
    public function column() : string;
}