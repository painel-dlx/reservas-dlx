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
use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Reservas\Entities\Reserva;
use Reservas\PainelDLX\Domain\Reservas\Exceptions\ReservaInvalidaException;
use Reservas\PainelDLX\Domain\Reservas\Validators\ValidarCancelarReserva;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ValidarCancelarReservaTest
 * @package Reservas\PainelDLX\Domain\Validators\Reservas
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Reservas\Validators\ValidarCancelarReserva
 */
class ValidarCancelarReservaTest extends ReservasTestCase
{
    /**
     * @throws Exception
     * @covers ::validar
     */
    public function test_Validar_deve_lancar_excecao_quando_Reserva_ja_cancelada()
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

        (new ValidarCancelarReserva())->validar($reserva);
    }

    /**
     * @throws ReservaInvalidaException
     * @covers ::validar
     */
    public function test_Validar_deve_retornar_true_quando_Reserva_for_valida_para_cancelar()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $data_inicial = new DateTime();
        $data_final = (clone $data_inicial)->modify('+2 days');

        $reserva = new Reserva($quarto, $data_inicial, $data_final, 1);
        $reserva->setId(0);

        $is_valida = (new ValidarCancelarReserva())->validar($reserva);
        $this->assertTrue($is_valida);
    }
}
