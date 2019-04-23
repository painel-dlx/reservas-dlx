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

namespace Reservas\PainelDLX\Domain\Entities;


use DLX\Domain\Entities\Entity;

class Pedido extends Entity
{
    const STATUS_PENDENTE = 'Pendente';
    const STATUS_PAGO = 'Pago';
    const STATUS_CANCELADO = 'Cancelado';

    /** @var int|null */
    private $id;
    /** @var string */
    private $nome;
    /** @var string */
    private $cpf;
    /** @var string */
    private $email;
    /** @var string */
    private $telefone;
    /** @var float */
    private $valor_total;
    /** @var string */
    private $formap_pgto = 'digitada';
    /** @var string */
    private $status = 'Pendente';
    /** @var PedidoPgtoCartao|null */
    private $pgto_cartao;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Pedido
     */
    public function setId(?int $id): Pedido
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * @param string $nome
     * @return Pedido
     */
    public function setNome(string $nome): Pedido
    {
        $this->nome = $nome;
        return $this;
    }

    /**
     * @return string
     */
    public function getCpf(): string
    {
        return $this->cpf;
    }

    /**
     * @param string $cpf
     * @return Pedido
     */
    public function setCpf(string $cpf): Pedido
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Pedido
     */
    public function setEmail(string $email): Pedido
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getTelefone(): string
    {
        return $this->telefone;
    }

    /**
     * @param string $telefone
     * @return Pedido
     */
    public function setTelefone(string $telefone): Pedido
    {
        $this->telefone = $telefone;
        return $this;
    }

    /**
     * @return float
     */
    public function getValorTotal(): float
    {
        return $this->valor_total;
    }

    /**
     * @param float $valor_total
     * @return Pedido
     */
    public function setValorTotal(float $valor_total): Pedido
    {
        $this->valor_total = $valor_total;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormapPgto(): string
    {
        return $this->formap_pgto;
    }

    /**
     * @param string $formap_pgto
     * @return Pedido
     */
    public function setFormapPgto(string $formap_pgto): Pedido
    {
        $this->formap_pgto = $formap_pgto;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Pedido
     */
    public function setStatus(string $status): Pedido
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return PedidoPgtoCartao|null
     */
    public function getPgtoCartao(): ?PedidoPgtoCartao
    {
        return $this->pgto_cartao;
    }

    /**
     * @param PedidoPgtoCartao|null $pgto_cartao
     * @return Pedido
     */
    public function setPgtoCartao(?PedidoPgtoCartao $pgto_cartao): Pedido
    {
        $this->pgto_cartao = $pgto_cartao;
        return $this;
    }
}