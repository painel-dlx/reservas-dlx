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

namespace Reservas\UseCases\Reservas\FiltrarReservasPorPeriodo;


use DateTime;
use Exception;
use Reservas\Domain\Reservas\Entities\Reserva;

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

        $data_incial = $this->defineDataIncial($command->getDataInicial());
        $data_final = $this->defineDataFinal($command->getDataFinal(), $data_incial);

        $lista_reservas = array_filter($command->getListaReservas(), function (Reserva $reserva) use ($data_incial, $data_final) {
            return $reserva->getCheckin()->format('Y-m-d') >= $data_incial->format('Y-m-d')
                && $reserva->getCheckin()->format('Y-m-d') <= $data_final->format('Y-m-d');
        });

        return $lista_reservas;
    }

    /**
     * Define a data inicial do período. Quando a data inicial não for informada, considera uma semana antes.
     * @param string $s_data_inicial
     * @return DateTime
     * @throws Exception
     */
    public function defineDataIncial(string $s_data_inicial): DateTime
    {
        $data_inicial = new DateTime($s_data_inicial);

        // Se a data inicial não foi informada, iniciar com as reservas de 1 semana atrás
        if (empty($s_data_inicial)) {
            $data_inicial->modify('-1 week');
        }

        return $data_inicial;
    }

    /**
     * Define a data final do período. Quando a data final não for informada, considera 1 mês a partir da ddata inicial
     * @param string $s_data_final
     * @param DateTime $data_inicial
     * @return DateTime
     * @throws Exception
     */
    public function defineDataFinal(string $s_data_final, DateTime $data_inicial): DateTime
    {
        return empty($s_data_final) ? (clone $data_inicial)->modify('+1 month') : new DateTime($s_data_final);
    }
}