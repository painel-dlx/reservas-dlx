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

namespace Reservas\PainelDLX\Domain\Disponibilidade\Entities;


use DLX\Domain\Entities\Entity;

/**
 * Class DisponValor
 * @package Reservas\PainelDLX\Domain\Entities
 * @covers DisponValorTest
 */
class DisponValor extends Entity
{
    /** @var Disponibilidade */
    private $dispon;
    /** @var int */
    private $qtde_pessoas;
    /** @var float */
    private $valor;

    /**
     * DisponValor constructor.
     * @param int $qtde_pessoas
     * @param float $valor
     */
    public function __construct(int $qtde_pessoas, float $valor)
    {
        $this->qtde_pessoas = $qtde_pessoas;
        $this->valor = $valor;
    }

    /**
     * @return Disponibilidade
     */
    public function getDispon(): Disponibilidade
    {
        return $this->dispon;
    }

    /**
     * @param Disponibilidade $dispon
     * @return DisponValor
     */
    public function setDispon(Disponibilidade $dispon): DisponValor
    {
        $this->dispon = $dispon;
        return $this;
    }

    /**
     * @return int
     */
    public function getQtdePessoas(): int
    {
        return $this->qtde_pessoas;
    }

    /**
     * @param int $qtde_pessoas
     * @return DisponValor
     */
    public function setQtdePessoas(int $qtde_pessoas): DisponValor
    {
        $this->qtde_pessoas = $qtde_pessoas;
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
     * @param float $valor
     * @return DisponValor
     */
    public function setValor(float $valor): DisponValor
    {
        $this->valor = $valor;
        return $this;
    }
}