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

namespace Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo;


use DateTime;
use Reservas\Domain\Quartos\Entities\Quarto;

/**
 * Class SalvarDisponPeriodoCommand
 * @package Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo
 * @covers SalvarDisponPeriodoCommandTest
 */
class SalvarDisponPeriodoCommand
{
    /**
     * @var DateTime
     */
    private $data_inicial;
    /**
     * @var DateTime
     */
    private $data_final;
    /**
     * @var Quarto
     */
    private $quarto;
    /**
     * @var int
     */
    private $qtde;
    /**
     * @var array
     */
    private $valores;

    /**
     * SalvarDisponPeriodoCommand constructor.
     * @param DateTime $data_inicial
     * @param DateTime $data_final
     * @param Quarto $quarto
     * @param int $qtde
     * @param array $valores
     */
    public function __construct(
        DateTime $data_inicial,
        DateTime $data_final,
        Quarto $quarto,
        int $qtde,
        array $valores
    ) {
        $this->data_inicial = $data_inicial;
        $this->data_final = $data_final;
        $this->quarto = $quarto;
        $this->qtde = $qtde;
        $this->valores = $valores;
    }

    /**
     * @return DateTime
     */
    public function getDataInicial(): DateTime
    {
        return $this->data_inicial;
    }

    /**
     * @return DateTime
     */
    public function getDataFinal(): DateTime
    {
        return $this->data_final;
    }

    /**
     * @return Quarto
     */
    public function getQuarto(): Quarto
    {
        return $this->quarto;
    }

    /**
     * @return int
     */
    public function getQtde(): int
    {
        return $this->qtde;
    }

    /**
     * @return array
     */
    public function getValores(): array
    {
        return $this->valores;
    }
}