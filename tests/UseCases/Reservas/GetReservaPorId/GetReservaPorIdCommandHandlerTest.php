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

namespace Reservas\Tests\UseCases\Reservas\GetReservaPorId;


use DLX\Infra\EntityManagerX;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\Domain\Reservas\Exceptions\ReservaNaoEncontradaException;
use Reservas\Domain\Reservas\Repositories\ReservaRepositoryInterface;
use Reservas\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommand;
use Reservas\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class GetReservaPorIdCommandHandlerTest
 * @package Reservas\UseCases\Reservas\GetReservaPorId
 * @coversDefaultClass \Reservas\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommandHandler
 */
class GetReservaPorIdCommandHandlerTest extends ReservasTestCase
{
    /**
     * @covers ::handle
     * @throws ReservaNaoEncontradaException
     */
    public function test_Handle_deve_lancar_excecao_quando_nao_encontrar_registro_no_bd()
    {
        $reserva_repository = $this->createMock(ReservaRepositoryInterface::class);
        $reserva_repository->method('find')->willReturn(null);

        /** @var ReservaRepositoryInterface $reserva_repository */

        $this->expectException(ReservaNaoEncontradaException::class);
        $this->expectExceptionCode(10);

        $command = new GetReservaPorIdCommand(0);
        (new GetReservaPorIdCommandHandler($reserva_repository))->handle($command);
    }

    /**
     * @covers ::handle
     * @throws ReservaNaoEncontradaException
     */
    public function test_Handle_deve_retornar_Reserva_quando_encontrar_registro_no_bd()
    {
        $reserva_id = mt_rand();

        $reserva = $this->createMock(Reserva::class);
        $reserva->method('getId')->willReturn($reserva_id);

        $reserva_repository = $this->createMock(ReservaRepositoryInterface::class);
        $reserva_repository->method('find')->willReturn($reserva);

        /** @var ReservaRepositoryInterface $reserva_repository */

        $command = new GetReservaPorIdCommand($reserva_id);
        $reserva_retornada = (new GetReservaPorIdCommandHandler($reserva_repository))->handle($command);

        $this->assertInstanceOf(Reserva::class, $reserva_retornada);
        $this->assertEquals($reserva_id, $reserva_retornada->getId());
    }
}
