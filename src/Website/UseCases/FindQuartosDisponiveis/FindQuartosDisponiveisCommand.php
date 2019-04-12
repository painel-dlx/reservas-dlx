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

namespace Reservas\Website\UseCases\FindQuartosDisponiveis;


use DateTime;

/**
 * Class FindQuartosDisponiveisCommand
 * @package Reservas\Website\UseCases\FindQuartosDisponiveis
 * @covers FindQuartosDisponiveisCommandTest
 */
class FindQuartosDisponiveisCommand
{
    /**
     * @var DateTime
     */
    private $data_entrada;
    /**
     * @var DateTime
     */
    private $data_saida;
    /**
     * @var int
     */
    private $qtde_adultos;
    /**
     * @var int
     */
    private $qtde_criancas;
    /**
     * @var int
     */
    private $qtde_quartos;

    /**
     * FindQuartosDisponiveisCommand constructor.
     * @param DateTime $data_entrada
     * @param DateTime $data_saida
     * @param int $qtde_adultos
     * @param int $qtde_criancas
     * @param int $qtde_quartos
     */
    public function __construct(DateTime $data_entrada, DateTime $data_saida, int $qtde_adultos = 2, int $qtde_criancas = 0, int $qtde_quartos = 1)
    {
        $this->data_entrada = $data_entrada;
        $this->data_saida = $data_saida;
        $this->qtde_adultos = $qtde_adultos;
        $this->qtde_criancas = $qtde_criancas;
        $this->qtde_quartos = $qtde_quartos;
    }

    /**
     * @return DateTime
     */
    public function getDataEntrada(): DateTime
    {
        return $this->data_entrada;
    }

    /**
     * @return DateTime
     */
    public function getDataSaida(): DateTime
    {
        return $this->data_saida;
    }

    /**
     * @return int
     */
    public function getQtdeAdultos(): int
    {
        return $this->qtde_adultos;
    }

    /**
     * @return int
     */
    public function getQtdeCriancas(): int
    {
        return $this->qtde_criancas;
    }

    /**
     * @return int
     */
    public function getQtdeQuartos(): int
    {
        return $this->qtde_quartos;
    }
}