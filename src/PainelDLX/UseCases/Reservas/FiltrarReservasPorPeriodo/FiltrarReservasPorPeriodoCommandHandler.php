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

namespace Reservas\PainelDLX\UseCases\Reservas\FiltrarReservasPorPeriodo;


use DateTime;
use Exception;
use Reservas\PainelDLX\Domain\Reservas\Entities\Reserva;

class FiltrarReservasPorPeriodoCommandHandler
{
    /**
     * @param FiltrarReservasPorPeriodoCommand $command
     * @return array
     * @throws Exception
     */
    public function handle(FiltrarReservasPorPeriodoCommand $command): array
    {
        if (empty($command->getListaReservas())) {
            return $command->getListaReservas();
        }

        $s_data_inicial = $command->getDataInicial();
        $s_data_final = $command->getDataFinal();

        $dt_data_inicial = new DateTime($s_data_inicial);
        $dt_data_final = new DateTime($s_data_final);

        if (empty($s_data_final)) {
            $dt_data_final->modify('+1 month');
        }

        $lista_reservas = array_filter($command->getListaReservas(), function (Reserva $reserva) use ($dt_data_inicial, $dt_data_final) {
            return $reserva->getCheckin()->format('Y-m-d') >= $dt_data_inicial->format('Y-m-d')
                && $reserva->getCheckin()->format('Y-m-d') <= $dt_data_final;
        });

        return $lista_reservas;
    }
}