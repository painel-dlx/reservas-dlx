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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Entities\Reserva;

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
    private $quantidade_adultos = 2;
    /** @var int */
    private $quantidade_criancas = 0;
    /** @var float|null */
    private $valor_total;
    /** @var Reserva|null */
    private $reserva;
    /** @var Collection */
    private $detalhamento;

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
        $this->checkin = (clone $checkin)->setTime(14, 0, 0);
        $this->checkout = (clone $checkout)->setTime(12, 0, 0);
        $this->quantidade = $quantidade;
        $this->quantidade_adultos = $adultos;
        $this->quantidade_criancas = $criancas;
        $this->detalhamento = new ArrayCollection();

        $this->calcularValorTotal();
        $this->defineDetalhamento();
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
    public function getQuantidadeAdultos(): int
    {
        return $this->quantidade_adultos;
    }

    /**
     * @return int
     */
    public function getQuantidadeCriancas(): int
    {
        return $this->quantidade_criancas;
    }

    /**
     * @return float|null
     */
    public function getValorTotal(): ?float
    {
        if (is_null($this->valor_total)) {
            $this->calcularValorTotal();
        }

        return $this->valor_total ?? 0;
    }

    /**
     * @return Reserva|null
     */
    public function getReserva(): ?Reserva
    {
        return $this->reserva;
    }

    /**
     * Verifica se a reserva referente a esse item foi gerada
     * @return bool
     */
    public function hasReservaGerada(): bool
    {
        return !is_null($this->getReserva());
    }

    /**
     * Calcular o valor total do item
     * @return void
     */
    private function calcularValorTotal(): void
    {
        $this->valor_total = 0;
        $qtde_hospedes = $this->getQuantidadeAdultos() + $this->getQuantidadeCriancas();

        $this->getQuarto()->getDisponibilidade($this->getCheckin(), $this->getCheckout())->map(function (Disponibilidade $disponibilidade) use ($qtde_hospedes) {
            $this->valor_total += $disponibilidade->getValorPorQtdePessoasComDesconto($qtde_hospedes);
        });

        $this->valor_total *= $this->getQuantidade();
    }

    /**
     * @return PedidoItem
     */
    private function defineDetalhamento(): self
    {
        $lista_dispon = $this->getQuarto()->getDisponibilidade($this->getCheckin(), $this->getCheckout());
        $qtde_pessoas = $this->getQuantidadeAdultos() + $this->getQuantidadeCriancas();

        /** @var Disponibilidade $disponibilidade */
        foreach ($lista_dispon as $disponibilidade) {
            $detalhe = new PedidoItemDetalhe(
                $this,
                $disponibilidade->getData(),
                $disponibilidade->getValorPorQtdePessoas($qtde_pessoas),
                $disponibilidade->getDesconto()
            );
            $this->getDetalhamento()->add($detalhe);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDetalhamento(): Collection
    {
        return $this->detalhamento;
    }

    /**
     * Verifica se esse item possui detalhamento
     * @return bool
     */
    public function hasDetalhamento(): bool
    {
        return !$this->getDetalhamento()->isEmpty();
    }

    /**
     * Gerar as reservas de acordo com as informações do item
     * @return $this
     */
    public function gerarReserva(): self
    {
        if (!$this->hasReservaGerada()) {
            $reserva = new Reserva(
                $this->getQuarto(),
                $this->getCheckin(),
                $this->getCheckout(),
                $this->getQuantidadeAdultos()
            );

            $reserva->setQuantidadeCriancas($this->getQuantidadeCriancas());
            $reserva->setValor($this->getValorTotal());

            $reserva->setHospede($this->getPedido()->getNome());
            $reserva->setCpf($this->getPedido()->getCpf());
            $reserva->setTelefone($this->getPedido()->getTelefone());
            $reserva->setEmail($this->getPedido()->getEmail());
            $reserva->setOrigem('Website');

            $this->reserva = $reserva;
        }

        return $this;
    }
}