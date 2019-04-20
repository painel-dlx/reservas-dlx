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

namespace Reservas\PainelDLX\UseCases\Reservas\ConfirmarReserva;


use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Repositories\ReservaRepositoryInterface;

class ConfirmarReservaCommandHandler
{
    /**
     * @var ReservaRepositoryInterface
     */
    private $reserva_repository;

    /**
     * ConfirmarReservaCommandHandler constructor.
     * @param ReservaRepositoryInterface $reserva_repository
     */
    public function __construct(ReservaRepositoryInterface $reserva_repository)
    {
        $this->reserva_repository = $reserva_repository;
    }

    /**
     * @param ConfirmarReservaCommand $command
     * @return Reserva
     */
    public function handle(ConfirmarReservaCommand $command): Reserva
    {
        $reserva = $command->getReserva();
        $reserva->confirmada($command->getMotivo(), $command->getUsuario());

        $this->reserva_repository->update($reserva);

        return $reserva;
    }
}