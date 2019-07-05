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
use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\Domain\Reservas\Exceptions\ReservaInvalidaException;
use Reservas\Domain\Reservas\Validators\ValidarConfirmarReserva;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ValidarConfirmarReservaTest
 * @package Reservas\Tests\Domain\Validators\Reservas
 * @coversDefaultClass \Reservas\Domain\Reservas\Validators\ValidarConfirmarReserva
 */
class ValidarConfirmarReservaTest extends ReservasTestCase
{
    /**
     * @param Quarto $quarto
     * @param DateTime $data_inicial
     * @param DateTime $data_final
     * @throws Exception
     */
    private function addDisponQuarto(Quarto $quarto, DateTime $data_inicial, DateTime $data_final): void
    {
        $periodo = new DatePeriod($data_inicial, new DateInterval('P1D'), $data_final);

        /** @var DateTime $data */
        foreach ($periodo as $data) {
            $quarto->addDispon($data, 10, [1 => 12.34]);
        }
    }

    /**
     * @throws ReservaInvalidaException
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

        $this->expectException(ReservaInvalidaException::class);
        $this->expectExceptionCode(10);
        (new ValidarConfirmarReserva())->validar($reserva);
    }

    /**
     * @throws ReservaInvalidaException
     * @throws Exception
     * @covers ::validar
     */
    public function test_Validar_deve_lancar_excecao_quando_reserva_for_confirmada()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $usuario = new Usuario('Funcionário', 'funcionario@aparthotel.com.br');
        $data_inicial = (new DateTime())->modify('+1 day');
        $data_final = (clone $data_inicial)->modify('+2 days');

        $this->addDisponQuarto($quarto, $data_inicial, $data_final);

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva->confirmada('Teste', $usuario);
        $reserva->setId(0);

        $this->expectException(ReservaInvalidaException::class);
        $this->expectExceptionCode(11);
        (new ValidarConfirmarReserva())->validar($reserva);
    }

    /**
     * @covers ::validar
     * @throws ReservaInvalidaException
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
