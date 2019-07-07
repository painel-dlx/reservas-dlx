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

namespace Reservas\UseCases\Pedidos\SalvarPedido;


class SalvarPedidoCommand
{
    /**
     * @var string
     */
    private $nome;
    /**
     * @var string
     */
    private $cpf;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $telefone;
    /**
     * @var float
     */
    private $valor_total;
    /**
     * @var array
     */
    private $itens;

    /**
     * SalvarPedidoCommand constructor.
     * @param string $nome
     * @param string $cpf
     * @param string $email
     * @param string $telefone
     * @param float $valor_total
     * @param array $itens
     */
    public function __construct(
        string $nome,
        string $cpf,
        string $email,
        string $telefone,
        float $valor_total,
        array $itens
    ) {
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->email = $email;
        $this->telefone = $telefone;
        $this->valor_total = $valor_total;
        $this->itens = $itens;
    }

    /**
     * @return string
     */
    public function getNome(): string
    {
        return $this->nome;
    }

    /**
     * @return string
     */
    public function getCpf(): string
    {
        return $this->cpf;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getTelefone(): string
    {
        return $this->telefone;
    }

    /**
     * @return float
     */
    public function getValorTotal(): float
    {
        return $this->valor_total;
    }

    /**
     * @return array
     */
    public function getItens(): array
    {
        return $this->itens;
    }
}