<?php

namespace Kuperwood\Eav\Interface;

interface DefineTableInterface
{
    public static function table() : string;
    public function column() : string;
}