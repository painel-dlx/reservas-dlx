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

/**
 * Class PedidoEndereco
 * @package Reservas\Domain\Pedidos\Entities
 * @covers PedidoEnderecoTest
 */
class PedidoEndereco extends Entity
{
    /** @var Pedido */
    private $pedido;
    /** @var string */
    private $cep;
    /** @var string */
    private $logradouro;
    /** @var string|null */
    private $numero;
    /** @var string */
    private $bairro;
    /** @var string */
    private $cidade;
    /** @var string */
    private $uf;
    /** @var string|null */
    private $complemento;

    /**
     * PedidoEndereco constructor.
     * @param Pedido $pedido
     * @param string $cep
     * @param string $logradouro
     * @param string|null $numero
     * @param string $bairro
     * @param string $cidade
     * @param string $uf
     * @param string|null $complemento
     */
    public function __construct(
        Pedido $pedido,
        string $cep,
        string $logradouro,
        ?string $numero,
        string $bairro,
        string $cidade,
        string $uf,
        ?string $complemento = null
    ) {
        $this->pedido = $pedido;
        $this->cep = $cep;
        $this->logradouro = $logradouro;
        $this->numero = $numero;
        $this->bairro = $bairro;
        $this->cidade = $cidade;
        $this->uf = $uf;
        $this->complemento = $complemento;
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
    public function getCep(): string
    {
        return $this->cep;
    }

    /**
     * @return string
     */
    public function getLogradouro(): string
    {
        return $this->logradouro;
    }

    /**
     * @return string|null
     */
    public function getNumero(): ?string
    {
        return $this->numero;
    }

    /**
     * @return string
     */
    public function getBairro(): string
    {
        return $this->bairro;
    }

    /**
     * @return string
     */
    public function getCidade(): string
    {
        return $this->cidade;
    }

    /**
     * @return string
     */
    public function getUf(): string
    {
        return $this->uf;
    }

    /**
     * @return string|null
     */
    public function getComplemento(): ?string
    {
        return $this->complemento;
    }
}