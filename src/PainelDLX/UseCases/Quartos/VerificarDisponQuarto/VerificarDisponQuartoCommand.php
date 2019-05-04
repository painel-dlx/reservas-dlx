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

namespace Reservas\PainelDLX\UseCases\Quartos\VerificarDisponQuarto;


use DateTime;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;

/**
 * Class VerificarDisponQuartoCommand
 * @package Reservas\Website\UseCases\VerificarDisponQuarto
 * @covers VerificarDisponQuartoCommandTest
 */
class VerificarDisponQuartoCommand
{
    /**
     * @var Quarto
     */
    private $quarto;
    /**
     * @var DateTime
     */
    private $checkin;
    /**
     * @var DateTime
     */
    private $checkout;
    /**
     * @var int
     */
    private $hospedes;
    /**
     * @var int
     */
    private $qtde_quartos;

    /**
     * VerificarDisponQuartoCommand constructor.
     * @param Quarto $quarto
     * @param DateTime $checkin
     * @param DateTime $checkout
     * @param int $hospedes
     * @param int $qtde_quartos
     */
    public function __construct(
        Quarto $quarto,
        DateTime $checkin,
        DateTime $checkout,
        int $hospedes,
        int $qtde_quartos = 1
    ) {
        $this->quarto = $quarto;
        $this->checkin = $checkin;
        $this->checkout = $checkout;
        $this->hospedes = $hospedes;
        $this->qtde_quartos = $qtde_quartos;
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
    public function getHospedes(): int
    {
        return $this->hospedes;
    }

    /**
     * @return int
     */
    public function getQtdeQuartos(): int
    {
        return $this->qtde_quartos;
    }
}