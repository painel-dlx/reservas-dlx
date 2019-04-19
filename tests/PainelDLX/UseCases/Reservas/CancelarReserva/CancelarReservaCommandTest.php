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

namespace Reservas\PainelDLX\Tests\UseCases\Reservas\CancelarReserva;

use DateTime;
use Exception;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use PHPUnit\Framework\TestCase;
use Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommand;

/**
 * Class CancelarReservaCommandTest
 * @package Reservas\PainelDLX\Tests\UseCases\Reservas\ConfirmarReserva
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommand
 */
class CancelarReservaCommandTest extends TestCase
{
    /**
     * @return CancelarReservaCommand
     * @throws Exception
     */
    public function test__construct(): CancelarReservaCommand
    {
        $quarto = new Quarto('Teste', 1, 10);
        $checkin = new DateTime();
        $checkout = (new DateTime())->modify('+3 days');

        $reserva = new Reserva($quarto, $checkin, $checkout, 1);
        $command = new CancelarReservaCommand($reserva, 'Teste');

        $this->assertInstanceOf(CancelarReservaCommand::class, $command);

        return $command;
    }

    /**
     * @param CancelarReservaCommand $command
     * @covers ::getReserva
     * @depends test__construct
     */
    public function test_GetReserva(CancelarReservaCommand $command)
    {
        $this->assertInstanceOf(Reserva::class, $command->getReserva());
    }

    /**
     * @param CancelarReservaCommand $command
     * @covers ::getMotivo
     * @depends test__construct
     */
    public function test_GetMotivo(CancelarReservaCommand $command)
    {
        $this->assertIsString($command->getMotivo());
    }
}
