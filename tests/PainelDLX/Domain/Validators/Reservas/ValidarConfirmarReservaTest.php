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
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Exceptions\ReservaJaCanceladaException;
use Reservas\PainelDLX\Domain\Exceptions\ReservaJaConfirmadaException;
use Reservas\PainelDLX\Domain\Validators\Reservas\ValidarConfirmarReserva;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ValidarConfirmarReservaTest
 * @package Reservas\PainelDLX\Tests\Domain\Validators\Reservas
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Validators\Reservas\ValidarConfirmarReserva
 */
class ValidarConfirmarReservaTest extends ReservasTestCase
{
    /**
     * @throws ReservaJaCanceladaException
     * @throws ReservaJaConfirmadaException
     * @covers ::validar
     */
    public function test_Validar_deve_lancar_excecao_quando_reserva_for_cancelada()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $usuario = new Usuario('Funcionário', 'funcionario@aparthotel.com.br');
        $data_inicial = new DateTime();
        $data_final = (clone $data_inicial)->modify('+2 days');

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva->cancelada('Teste', $usuario);
        $reserva->setId(0);

        $this->expectException(ReservaJaCanceladaException::class);
        (new ValidarConfirmarReserva())->validar($reserva);
    }

    /**
     * @throws ReservaJaCanceladaException
     * @throws ReservaJaConfirmadaException
     * @covers ::validar
     */
    public function test_Validar_deve_lancar_excecao_quando_reserva_for_confirmada()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $usuario = new Usuario('Funcionário', 'funcionario@aparthotel.com.br');
        $data_inicial = new DateTime();
        $data_final = (clone $data_inicial)->modify('+2 days');

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva->confirmada('Teste', $usuario);
        $reserva->setId(0);

        $this->expectException(ReservaJaConfirmadaException::class);
        (new ValidarConfirmarReserva())->validar($reserva);
    }

    /**
     * @throws ReservaJaCanceladaException
     * @throws ReservaJaConfirmadaException
     * @covers ::validar
     */
    public function test_Validar_deve_retornar_true_quando_reserva_puder_ser_confirmada()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $data_inicial = new DateTime();
        $data_final = (clone $data_inicial)->modify('+2 days');

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva->setId(0);

        $is_valida = (new ValidarConfirmarReserva())->validar($reserva);
        $this->assertTrue($is_valida);
    }
}
