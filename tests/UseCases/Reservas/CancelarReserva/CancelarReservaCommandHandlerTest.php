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

namespace Reservas\Tests\UseCases\Reservas\CancelarReserva;

use DateTime;
use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\Domain\Reservas\Repositories\ReservaRepositoryInterface;
use Reservas\UseCases\Reservas\CancelarReserva\CancelarReservaCommand;
use Reservas\UseCases\Reservas\CancelarReserva\CancelarReservaCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class CancelarReservaCommandHandlerTest
 * @package Reservas\Tests\UseCases\Reservas\CancelarReserva
 * @coversDefaultClass \Reservas\UseCases\Reservas\CancelarReserva\CancelarReservaCommandHandler
 */
class CancelarReservaCommandHandlerTest extends ReservasTestCase
{
    /**
     * @throws Exception
     * @covers ::handle
     */
    public function test_Handle_deve_configurar_Reserva_como_cancelada()
    {
        $reserva_repository = $this->createMock(ReservaRepositoryInterface::class);
        $reserva_repository->method('update')->willReturn(null);

        $quarto = $this->createMock(Quarto::class);
        // $quarto->method('isDisponivelPeriodo')->willReturn(true);

        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getId')->willReturn(mt_rand());

        /** @var ReservaRepositoryInterface $reserva_repository */
        /** @var Quarto $quarto */
        /** @var Usuario $usuario */

        $checkin = (new DateTime())->modify('+1 day');
        $checkout = (clone $checkin)->modify('+3 days');

        $reserva = new Reserva($quarto, $checkin, $checkout, 1);

        $command = new CancelarReservaCommand($reserva, $usuario, 'Teste');
        (new CancelarReservaCommandHandler($reserva_repository))->handle($command);

        $this->assertTrue($reserva->isCancelada());
    }
}
