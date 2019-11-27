<?php
/**
 * MIT License
 *
 * Copyright (c) 2018 PHP DLX
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Reservas\Domain\Pedidos\Entities;


use DateTime;
use DLX\Domain\Entities\Entity;

/**
 * Class PedidoItemDetalhe
 * @package Reservas\Domain\Pedidos\Entities
 * @covers PedidoItemDetalheTest
 */
class PedidoItemDetalhe extends Entity
{
    /** @var PedidoItem */
    private $item;
    /** @var DateTime */
    private $data;
    /** @var float */
    private $diaria;
    /** @var float */
    private $desconto;

    /**
     * PedidoItemDetalhe constructor.
     * @param PedidoItem $item
     * @param DateTime $data
     * @param float $diaria
     * @param float $desconto
     */
    public function __construct(PedidoItem $item, DateTime $data, float $diaria, float $desconto)
    {
        $this->item = $item;
        $this->data = $data;
        $this->diaria = $diaria;
        $this->desconto = $desconto;
    }

    /**
     * @return PedidoItem
     */
    public function getItem(): PedidoItem
    {
        return $this->item;
    }

    /**
     * @return DateTime
     */
    public function getData(): DateTime
    {
        return $this->data;
    }

    /**
     * @return float
     */
    public function getDiaria(): float
    {
        return $this->diaria;
    }

    /**
     * @return float
     */
    public function getDesconto(): float
    {
        return $this->desconto;
    }

    /**
     * @return string
     */
    public function getPercentDesconto(): string
    {
        return ($this->desconto * 100) . '%';
    }
}