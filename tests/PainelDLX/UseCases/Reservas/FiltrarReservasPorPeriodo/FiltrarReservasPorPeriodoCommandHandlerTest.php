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

namespace Reservas\PainelDLX\Tests\UseCases\Reservas\FiltrarReservasPorPeriodo;

use DateTime;
use Exception;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Reservas\Entities\Reserva;
use Reservas\PainelDLX\UseCases\Reservas\FiltrarReservasPorPeriodo\FiltrarReservasPorPeriodoCommand;
use Reservas\PainelDLX\UseCases\Reservas\FiltrarReservasPorPeriodo\FiltrarReservasPorPeriodoCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class FiltrarReservasPorPeriodoCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Reservas\FiltrarReservasPorPeriodo
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Reservas\FiltrarReservasPorPeriodo\FiltrarReservasPorPeriodoCommandHandler
 */
class FiltrarReservasPorPeriodoCommandHandlerTest extends ReservasTestCase
{

    /**
     * @throws Exception
     * @covers ::defineDataFinal
     */
    public function test_DefineDataIncial_deve_definir_data_de_uma_semana_atras_quando_data_inicial_nao_informada()
    {
        $handler = new FiltrarReservasPorPeriodoCommandHandler();
        $data_inicial = $handler->defineDataIncial('');
        $uma_semana_atras = (new DateTime())->modify('-1 week');


        $this->assertEquals($uma_semana_atras, $data_inicial, '', 0.1);
    }

    /**
     * @throws Exception
     * @covers ::defineDataIncial
     */
    public function test_DefineDataFinal_deve_definir_periodo_um_mes_quando_data_final_nao_informada()
    {
        $handler = new FiltrarReservasPorPeriodoCommandHandler();
        $data_inicial = new DateTime();
        $data_final = $handler->defineDataFinal('', $data_inicial);
        $diff = $data_final->diff($data_inicial);

        $this->assertEquals(1, $diff->m);
    }

    /**
     * @throws Exception
     * @covers ::handle
     */
    public function test_Handle_deve_manter_no_array_apenas_reservas_dentro_do_periodo()
    {
        $quarto = new Quarto('Quarto de Teste', 10, 10);
        $data_inicial = new DateTime();
        $data_final = (clone $data_inicial)->modify('+10 days');

        $reserva1 = new Reserva($quarto, $data_inicial, (clone $data_inicial)->modify('+2 days'), 2);
        $reserva2 = new Reserva($quarto, (clone $data_inicial)->modify('-2 days'), $data_final, 1);

        $lista_reservas = [$reserva1, $reserva2];

        $command = new FiltrarReservasPorPeriodoCommand($lista_reservas, $data_inicial->format('Y-m-d'), $data_final->format('Y-m-d'));
        $lista_reservas_filtradas = (new FiltrarReservasPorPeriodoCommandHandler())->handle($command);

        $this->assertTrue(count($lista_reservas) !== count($lista_reservas_filtradas));
    }
}
