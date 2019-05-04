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

namespace Reservas\PainelDLX\Tests\UseCases\Reservas\ListaReservas;

use DLX\Infra\EntityManagerX;
use Reservas\PainelDLX\Domain\Reservas\Entities\Reserva;
use Reservas\PainelDLX\Domain\Reservas\Repositories\ReservaRepositoryInterface;
use Reservas\PainelDLX\UseCases\Reservas\ListaReservas\ListaReservasCommand;
use Reservas\PainelDLX\UseCases\Reservas\ListaReservas\ListaReservasCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ListaReservasCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Reservas\ListaReservas
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Reservas\ListaReservas\ListaReservasCommandHandler
 */
class ListaReservasCommandHandlerTest extends ReservasTestCase
{
    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function test__construct(): ListaReservasCommandHandler
    {
        /** @var ReservaRepositoryInterface $reserva_repository */
        $reserva_repository = EntityManagerX::getRepository(Reserva::class);

        $handler = new ListaReservasCommandHandler($reserva_repository);
        $this->assertInstanceOf(ListaReservasCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param ListaReservasCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_retornar_um_array_com_Reservas(ListaReservasCommandHandler $handler)
    {
        $command = new ListaReservasCommand([], [], 10);
        $lista = $handler->handle($command);

        $this->assertIsArray($lista);

        if (count($lista) > 0) {
            /** @var \Reservas\PainelDLX\Domain\Reservas\Entities\Reserva $reserva */
            foreach ($lista as $reserva) {
                $this->assertInstanceOf(Reserva::class, $reserva);
            }
        }
    }
}
