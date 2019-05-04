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

namespace Reservas\PainelDLX\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo;


use DateTime;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;

class ListaDisponibilidadePorPeriodoCommand
{
    /**
     * @var DateTime
     */
    private $data_inicio;
    /**
     * @var DateTime
     */
    private $data_final;
    /**
     * @var Quarto|null
     */
    private $quarto;

    /**
     * ListaDisponibilidadePorPeriodoCommand constructor.
     * @param DateTime $data_inicio
     * @param DateTime $data_final
     * @param Quarto|null $quarto
     */
    public function __construct(DateTime $data_inicio, DateTime $data_final, ?Quarto $quarto = null)
    {
        $this->data_inicio = $data_inicio;
        $this->data_final = $data_final;
        $this->quarto = $quarto;
    }

    /**
     * @return DateTime
     */
    public function getDataInicio(): DateTime
    {
        return $this->data_inicio;
    }

    /**
     * @return DateTime
     */
    public function getDataFinal(): DateTime
    {
        return $this->data_final;
    }

    /**
     * @return Quarto|null
     */
    public function getQuarto(): ?Quarto
    {
        return $this->quarto;
    }
}