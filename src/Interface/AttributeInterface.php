<?php

namespace Kuperwood\Eav\Interface;

interface AttributeInterface
{
    public function create(array $input) : array;
    public function update(int $id, array $input) : array;
    public function delete(int $id) : bool;
    public function findOne(int $id) : ?array;
    public function findMany(array $criteria) : array;
}