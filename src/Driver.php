<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav;

use Drobotik\Eav\Interface\DriverInterface;

abstract class Driver implements DriverInterface
{
    private int $total;
    private int $cursor;
    private int $chunkSize;
    protected array $header;

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

    public function getHeader(): array
    {
        return $this->header;
    }

    public function setHeader(array $columns): void
    {
        $this->header = $columns;
    }

    public function isHeader(): bool
    {
        return isset($this->header);
    }

}