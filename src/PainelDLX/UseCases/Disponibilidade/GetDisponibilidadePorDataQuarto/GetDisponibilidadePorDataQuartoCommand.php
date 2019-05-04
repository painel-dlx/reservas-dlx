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

namespace Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto;


use DateTime;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;

/**
 * Class GetDisponibilidadePorDataQuartoCommand
 * @package Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto
 * @covers GetDisponibilidadePorDataQuartoCommandTest
 */
class GetDisponibilidadePorDataQuartoCommand
{
    /**
     * @var Quarto
     */
    private $quarto;
    /**
     * @var DateTime
     */
    private $data;

    /**
     * GetDisponibilidadePorDataQuartoCommand constructor.
     * @param \Reservas\PainelDLX\Domain\Quartos\Entities\Quarto $quarto
     * @param DateTime $data
     */
    public function __construct(Quarto $quarto, DateTime $data)
    {
        $this->quarto = $quarto;
        $this->data = $data;
    }

    /**
     * @return \Reservas\PainelDLX\Domain\Quartos\Entities\Quarto
     */
    public function getQuarto(): Quarto
    {
        return $this->quarto;
    }

    /**
     * @return DateTime
     */
    public function getData(): DateTime
    {
        return $this->data;
    }
}