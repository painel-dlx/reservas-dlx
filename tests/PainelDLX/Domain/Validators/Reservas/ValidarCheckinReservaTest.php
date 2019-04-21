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

namespace Reservas\PainelDLX\Tests\Domain\Validators\Reservas;

use DateTime;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Exceptions\CheckinAnteriorHojeException;
use Reservas\PainelDLX\Domain\Validators\Reservas\ValidarCheckinReserva;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidarCheckinReservaTest
 * @package Reservas\PainelDLX\Tests\Domain\Validators\Reservas
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Validators\Reservas\ValidarCheckinReserva
 */
class ValidarCheckinReservaTest extends TestCase
{

    /**
     * @throws CheckinAnteriorHojeException
     * @covers ::validar
     */
    public function test_Validar_deve_lancar_excecao_quando_data_checkin_for_menor_hoje()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $data_inicial = (new DateTime())->modify('-1 day');
        $data_final = (clone $data_inicial)->modify('+2 days');

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva->setId(0);

        $this->expectException(CheckinAnteriorHojeException::class);
        (new ValidarCheckinReserva())->validar($reserva);
    }

    /**
     * @throws CheckinAnteriorHojeException
     * @covers ::validar
     */
    public function test_Validar_deve_retornar_true_quando_checkin_valido()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $data_inicial = new DateTime();
        $data_final = (clone $data_inicial)->modify('+2 days');

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva->setId(0);

        $is_valida = (new ValidarCheckinReserva())->validar($reserva);
        $this->assertTrue($is_valida);
    }
}
