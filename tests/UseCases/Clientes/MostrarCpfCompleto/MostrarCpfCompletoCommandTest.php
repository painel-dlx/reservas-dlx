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

namespace Reservas\Tests\UseCases\Clientes\MostrarCpfCompleto;

use DateTime;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommand;
use PHPUnit\Framework\TestCase;

/**
 * Class MostrarCpfCompletoCommandTest
 * @package Reservas\Tests\UseCases\Clientes\MostrarCpfCompleto
 * @coversDefaultClass \Reservas\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommand
 */
class MostrarCpfCompletoCommandTest extends TestCase
{

    public function test__construct(): MostrarCpfCompletoCommand
    {
        $quarto = new Quarto('Teste de Quarto', 10, 10);
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+2 days');

        $reserva =  new Reserva($quarto, $checkin, $checkout, 2);
        $usuario = new Usuario('Teste de Funcionário', 'funcionario@gmail.com');

        $command = new MostrarCpfCompletoCommand($reserva, $usuario);
        $this->assertInstanceOf(MostrarCpfCompletoCommand::class, $command);
        return $command;
    }

    /**
     * @param MostrarCpfCompletoCommand $command
     * @covers ::getReserva
     * @depends test__construct
     */
    public function test_GetReserva(MostrarCpfCompletoCommand $command)
    {
        $this->assertInstanceOf(Reserva::class, $command->getReserva());
    }

    /**
     * @param MostrarCpfCompletoCommand $command
     * @covers ::getUsuario
     * @depends test__construct
     */
    public function test_GetUsuario(MostrarCpfCompletoCommand $command)
    {
        $this->assertInstanceOf(Usuario::class, $command->getUsuario());
    }
}
