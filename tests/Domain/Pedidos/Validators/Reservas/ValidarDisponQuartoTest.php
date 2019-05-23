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

namespace Reservas\Tests\Domain\Validators\Reservas;

use DateInterval;
use DatePeriod;
use DateTime;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Exceptions\QuartoIndisponivelException;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\Domain\Reservas\Validators\ValidarDisponQuarto;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ValidarDisponQuartoTest
 * @package Reservas\Domain\Validators\Reservas
 * @coversDefaultClass \Reservas\Domain\Reservas\Validators\ValidarDisponQuarto
 */
class ValidarDisponQuartoTest extends ReservasTestCase
{
    /**
     * @covers ::validar
     * @throws QuartoIndisponivelException
     */
    public function test_Validar_deve_lancar_excecao_quando_quarto_nao_estiver_disponivel_em_algum_dia()
    {
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+1 day');
        $quarto = new Quarto('Teste', 10, 10);

        $reserva = new Reserva($quarto, $checkin, $checkout, 0);

        $this->expectException(QuartoIndisponivelException::class);
        $this->expectExceptionCode(10);
        (new ValidarDisponQuarto())->validar($reserva);
    }

    /**
     * @covers ::validar
     * @throws QuartoIndisponivelException
     */
    public function test_Validar_retornar_true_quando_quarto_estiver_disponivel_todos_dias_da_reserva()
    {
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+1 day');
        $quarto = new Quarto('Teste', 10, 10);

        $dt_interval = new DateInterval('P1D');
        $dt_periodo = new DatePeriod($checkin, $dt_interval, $checkout);

        /** @var DateTime $data */
        foreach ($dt_periodo as $data) {
            $quarto->addDispon($data, 1, [1 => 10]);
        }

        $reserva = new Reserva($quarto, $checkin, $checkout, 0);

        $is_diponivel = (new ValidarDisponQuarto())->validar($reserva);
        $this->assertTrue($is_diponivel);
    }
}
