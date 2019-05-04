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
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Exceptions\DataCheckoutMenorCheckinException;
use Reservas\PainelDLX\Domain\Validators\Reservas\ValidarCheckoutReserva;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ValidarCheckoutReservaTest
 * @package Reservas\PainelDLX\Tests\Domain\Validators\Reservas
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Validators\Reservas\ValidarCheckoutReserva
 */
class ValidarCheckoutReservaTest extends ReservasTestCase
{

    /**
     * @throws DataCheckoutMenorCheckinException
     * @covers ::validar
     */
    public function test_Validar_deve_lancar_excecao_quando_checkin_for_maior_checkout()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $data_inicial = new DateTime();
        $data_final = (new DateTime())->modify('-1 day');

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva->setId(0);

        $this->expectException(DataCheckoutMenorCheckinException::class);
        (new ValidarCheckoutReserva())->validar($reserva);
    }

    /**
     * @throws DataCheckoutMenorCheckinException
     * @covers ::validar
     */
    public function test_Validar_deve_retornar_true_quando_data_checkout_for_valida()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $data_inicial = new DateTime();
        $data_final = (new DateTime())->modify('+1 day');

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva->setId(0);

        $is_valido = (new ValidarCheckoutReserva())->validar($reserva);
        $this->assertTrue($is_valido);
    }
}
