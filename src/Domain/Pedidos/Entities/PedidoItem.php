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
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Quartos\Entities\Quarto;

/**
 * Class PedidoItem
 * @package Reservas\Domain\Pedidos\Entities
 * @covers PedidoItemTest
 */
class PedidoItem extends Entity
{
    /** @var int|null */
    private $id;
    /** @var Pedido */
    private $pedido;
    /** @var Quarto */
    private $quarto;
    /** @var DateTime */
    private $checkin;
    /** @var DateTime */
    private $checkout;
    /** @var int */
    private $quantidade = 1;
    /** @var int */
    private $adultos = 2;
    /** @var int */
    private $criancas = 0;
    /** @var float|null */
    private $valor_total;

    /**
     * PedidoItem constructor.
     * @param Pedido $pedido
     * @param Quarto $quarto
     * @param DateTime $checkin
     * @param DateTime $checkout
     * @param int $quantidade
     * @param int $adultos
     * @param int $criancas
     */
    public function __construct(
        Pedido $pedido,
        Quarto $quarto,
        DateTime $checkin,
        DateTime $checkout,
        int $quantidade = 1,
        int $adultos = 2,
        int $criancas = 0
    ) {
        $this->pedido = $pedido;
        $this->quarto = $quarto;
        $this->checkin = $checkin->setTime(14, 0, 0);
        $this->checkout = $checkout->setTime(12, 0, 0);
        $this->quantidade = $quantidade;
        $this->adultos = $adultos;
        $this->criancas = $criancas;

        $this->calcularValorTotal();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Pedido
     */
    public function getPedido(): Pedido
    {
        return $this->pedido;
    }

    /**
     * @return Quarto
     */
    public function getQuarto(): Quarto
    {
        return $this->quarto;
    }

    /**
     * @return DateTime
     */
    public function getCheckin(): DateTime
    {
        return $this->checkin;
    }

    /**
     * @return DateTime
     */
    public function getCheckout(): DateTime
    {
        return $this->checkout;
    }

    /**
     * @return int
     */
    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    /**
     * @return int
     */
    public function getAdultos(): int
    {
        return $this->adultos;
    }

    /**
     * @return int
     */
    public function getCriancas(): int
    {
        return $this->criancas;
    }

    /**
     * @return float|null
     */
    public function getValorTotal(): ?float
    {
        //$this->calcularValorTotal();
        return $this->valor_total ?? 0;
    }

    /**
     * Calcular o valor total do item
     * @return void
     */
    private function calcularValorTotal(): void
    {
        $this->valor_total = 0;
        $qtde_hospedes = $this->getAdultos() + $this->getCriancas();

        $this->getQuarto()->getDispon($this->getCheckin(), $this->getCheckout())->map(function (Disponibilidade $disponibilidade) use ($qtde_hospedes) {
            $this->valor_total += $disponibilidade->getValorPorQtdePessoas($qtde_hospedes);
        });

        $this->valor_total *= $qtde_hospedes;
    }
}