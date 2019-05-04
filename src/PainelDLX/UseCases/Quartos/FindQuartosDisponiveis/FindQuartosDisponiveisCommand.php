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

namespace Reservas\PainelDLX\UseCases\Quartos\FindQuartosDisponiveis;


use DateTime;

class FindQuartosDisponiveisCommand
{
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
    private $qtde_hospedes;
    /**
     * @var int
     */
    private $qde_quartos;

    /**
     * FindQuartosDisponiveisCommand constructor.
     * @param DateTime $checkin
     * @param DateTime $checkout
     * @param int $qtde_hospedes
     * @param int $qde_quartos
     */
    public function __construct(
        DateTime $checkin,
        DateTime $checkout,
        int $qtde_hospedes,
        int $qde_quartos
    ) {
        $this->checkin = $checkin;
        $this->checkout = $checkout;
        $this->qtde_hospedes = $qtde_hospedes;
        $this->qde_quartos = $qde_quartos;
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
    public function getQtdeHospedes(): int
    {
        return $this->qtde_hospedes;
    }

    /**
     * @return int
     */
    public function getQdeQuartos(): int
    {
        return $this->qde_quartos;
    }
}