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

namespace Reservas\Tests\UseCases\Reservas\ConfirmarReserva;

use DateTime;
use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommand;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfirmarReservaCommandTest
 * @package Reservas\Tests\UseCases\Reservas\ConfirmarReserva
 * @coversDefaultClass \Reservas\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommand
 */
class ConfirmarReservaCommandTest extends TestCase
{
    /**
     * @return ConfirmarReservaCommand
     * @throws Exception
     */
    public function test__construct(): ConfirmarReservaCommand
    {
        $quarto = new Quarto('Teste', 1, 10);
        $checkin = new DateTime();
        $checkout = (new DateTime())->modify('+3 days');
        $usuario = new Usuario('Usuário de Teste', 'teste@unitario.com');

        $reserva = new Reserva($quarto, $checkin, $checkout, 1);
        $command = new ConfirmarReservaCommand($reserva, $usuario, 'Teste');

        $this->assertInstanceOf(ConfirmarReservaCommand::class, $command);

        return $command;
    }

    /**
     * @param ConfirmarReservaCommand $command
     * @covers ::getReserva
     * @depends test__construct
     */
    public function test_GetReserva(ConfirmarReservaCommand $command)
    {
        $this->assertInstanceOf(Reserva::class, $command->getReserva());
    }

    /**
     * @param ConfirmarReservaCommand $command
     * @covers ::getUsuario
     * @depends test__construct
     */
    public function testGetUsuario(ConfirmarReservaCommand $command)
    {
        $this->assertInstanceOf(Usuario::class, $command->getUsuario());
    }

    /**
     * @param ConfirmarReservaCommand $command
     * @covers ::getMotivo
     * @depends test__construct
     */
    public function test_GetMotivo(ConfirmarReservaCommand $command)
    {
        $this->assertIsString($command->getMotivo());
    }
}
