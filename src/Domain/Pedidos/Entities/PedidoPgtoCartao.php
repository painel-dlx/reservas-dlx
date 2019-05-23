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
use Reservas\Domain\Pedidos\Entities\Pedido;

class PedidoPgtoCartao extends Entity
{
    /** @var Pedido */
    private $pedido;
    /** @var float */
    private $valor;
    /** @var string */
    private $numero_cartao;
    /** @var string */
    private $dono_cartao;
    /** @var string */
    private $expiracao;
    /** @var string */
    private $codigo_seguranca;
    /** @var int */
    private $qtde_parcelas = 1;

    /**
     * PedidoPgtoCartao constructor.
     * @param string $numero_cartao
     * @param string $dono_cartao
     * @param string $expiracao
     * @param string $codigo_seguranca
     */
    public function __construct(string $numero_cartao, string $dono_cartao, string $expiracao, string $codigo_seguranca)
    {
        $this->numero_cartao = $numero_cartao;
        $this->dono_cartao = $dono_cartao;
        $this->expiracao = $expiracao;
        $this->codigo_seguranca = $codigo_seguranca;
    }

    /**
     * @return Pedido
     */
    public function getPedido(): Pedido
    {
        return $this->pedido;
    }

    /**
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
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
    public function getDonoCartao(): string
    {
        return $this->dono_cartao;
    }

    /**
     * @return string
     */
    public function getExpiracao(): string
    {
        return $this->expiracao;
    }

    /**
     * @return string
     */
    public function getCodigoSeguranca(): string
    {
        return $this->codigo_seguranca;
    }

    /**
     * @return int
     */
    public function getQtdeParcelas(): int
    {
        return $this->qtde_parcelas;
    }
}