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

namespace Reservas\Domain\Disponibilidade\Entities;


use DLX\Domain\Entities\Entity;

/**
 * Class DisponValor
 * @package Reservas\Domain\Entities
 * @covers DisponValorTest
 */
class DisponibilidadeValor extends Entity
{
    /** @var Disponibilidade */
    private $disponibilidade;
    /** @var int */
    private $quantidade_pessoas;
    /** @var float */
    private $valor;

    /**
     * DisponValor constructor.
     * @param int $quantidade_pessoas
     * @param float $valor
     */
    public function __construct(int $quantidade_pessoas, float $valor)
    {
        $this->quantidade_pessoas = $quantidade_pessoas;
        $this->valor = $valor;
    }

    /**
     * @return Disponibilidade
     */
    public function getDisponibilidade(): Disponibilidade
    {
        return $this->disponibilidade;
    }

    /**
     * @param Disponibilidade $disponibilidade
     * @return DisponibilidadeValor
     */
    public function setDisponibilidade(Disponibilidade $disponibilidade): DisponibilidadeValor
    {
        $this->disponibilidade = $disponibilidade;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantidadePessoas(): int
    {
        return $this->quantidade_pessoas;
    }

    /**
     * @param int $quantidade_pessoas
     * @return DisponibilidadeValor
     */
    public function setQuantidadePessoas(int $quantidade_pessoas): DisponibilidadeValor
    {
        $this->quantidade_pessoas = $quantidade_pessoas;
        return $this;
    }

    /**
     * @return float
     */
    public function getValor(): float
    {
        return $this->valor;
    }

    /**
     * Valor com desconto
     * @return float
     */
    public function getValorComDesconto(): float
    {
        $valor_total = $this->getValor();
        $valor_desconto = $valor_total * $this->getDisponibilidade()->getDesconto();

        return $valor_total - $valor_desconto;
    }

    /**
     * @param float $valor
     * @return DisponibilidadeValor
     */
    public function setValor(float $valor): DisponibilidadeValor
    {
        $this->valor = $valor;
        return $this;
    }
}