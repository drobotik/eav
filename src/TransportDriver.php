<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav;

use Drobotik\Eav\Interface\TransportDriverInterface;

abstract class TransportDriver implements TransportDriverInterface
{
    private int $total;
    private int $cursor;
    private int $chunkSize;

    public function getChunkSize() : int
    {
        return $this->chunkSize;
    }

    public function setChunkSize(int $size)
    {
        $this->chunkSize = $size;
    }

    public function getCursor() : int
    {
        return $this->cursor;
    }

    public function setCursor(int $cursor) : void
    {
        $this->cursor = $cursor;
    }

    public function setTotal(int $total)
    {
        $this->total = $total;
    }

    public function getTotal() : int
    {
        return $this->total;
    }
}