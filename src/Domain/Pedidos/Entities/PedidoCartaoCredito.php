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


use DLX\Domain\Entities\Entity;
use Reservas\Domain\Pedidos\Services\IdentificarBandeiraCartao;

/**
 * Class PedidoCartaoCredito
 * @package Reservas\Domain\Pedidos\Entities
 * @covers PedidoCartaoCreditoTest
 */
class PedidoCartaoCredito extends Entity
{
    /** @var Pedido */
    private $pedido;
    /** @var string */
    private $dono;
    /** @var string|null */
    private $bandeira;
    /** @var string */
    private $numero_cartao;
    /** @var string */
    private $validade;
    /** @var string */
    private $codigo_seguranca;
    /** @var int */
    private $parcelas = 1;
    /** @var float */
    private $valor;

    /**
     * PedidoPgtoCartao constructor.
     * @param Pedido $pedido
     * @param string $dono
     * @param string $numero_cartao
     * @param string $validade
     * @param string $codigo_seguranca
     * @param int $parcelas
     */
    public function __construct(
        Pedido $pedido,
        string $dono,
        string $numero_cartao,
        string $validade,
        string $codigo_seguranca,
        int $parcelas = 1
    ) {
        $this->pedido = $pedido;
        $this->dono = strtoupper($dono);
        $this->numero_cartao = $numero_cartao;
        $this->validade = $validade;
        $this->codigo_seguranca = $codigo_seguranca;
        $this->parcelas = $parcelas;
        $this->valor = $pedido->getValorTotal();

        $this->identificarBandeira();
    }

    /**
     * @return Pedido
     */
    public function getPedido(): Pedido
    {
        return $this->pedido;
    }

    /**
     * @return string
     */
    public function getDono(): string
    {
        return $this->dono;
    }

    /**
     * @return string|null
     */
    public function getBandeira(): ?string
    {
        if (is_null($this->bandeira)) {
            $this->identificarBandeira();
        }

        return $this->bandeira;
    }

    /**
     * @return string
     */
    public function getNumeroCartao(): string
    {
        return $this->numero_cartao;
    }

    /**
     * @return string
     */
    public function getValidade(): string
    {
        return $this->validade;
    }

    /**
     * @return string
     */
    public function getCodigoSeguranca(): string
    {
        return $this->codigo_seguranca;
    }

    /**
     * @return float
     */
    public function getValor(): float
    {
        $this->valor = $this->getPedido()->getValorTotal();
        return $this->valor;
    }

    /**
     * @return int
     */
    public function getParcelas(): int
    {
        return $this->parcelas;
    }

    /**
     * Identificar a bandeira do número de cartão de crédito
     * @return PedidoCartaoCredito
     */
    private function identificarBandeira(): self
    {
        $this->bandeira = (new IdentificarBandeiraCartao())->executar($this->getNumeroCartao());
        return $this;
    }
}