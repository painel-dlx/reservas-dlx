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

namespace Reservas\PainelDLX\UseCases\Reservas\SalvarReserva;


use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Repositories\ReservaRepositoryInterface;
use Reservas\PainelDLX\Domain\Validators\ReservaValidator;
use Reservas\PainelDLX\Domain\Validators\ReservaValidatorsEnum;

class SalvarReservaCommandHandler
{
    /**
     * @var ReservaRepositoryInterface
     */
    private $reserva_repository;

    /**
     * ReservarQuartoCommandHandler constructor.
     * @param ReservaRepositoryInterface $reserva_repository
     */
    public function __construct(ReservaRepositoryInterface $reserva_repository)
    {
        $this->reserva_repository = $reserva_repository;
    }

    /**
     * @param SalvarReservaCommand $command
     * @return Reserva
     */
    public function handle(SalvarReservaCommand $command): Reserva
    {
        $reserva = $command->getReserva();

        $validator = new ReservaValidator(ReservaValidatorsEnum::SALVAR);
        $validator->validar($reserva);

        if (is_null($reserva->getId())) {
            $this->reserva_repository->create($command->getReserva());
        } else {
            $this->reserva_repository->update($command->getReserva());
        }

        return $reserva;
    }
}