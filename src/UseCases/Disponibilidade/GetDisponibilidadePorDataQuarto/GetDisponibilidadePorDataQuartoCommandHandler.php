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

namespace Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto;


use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;

/**
 * Class GetDisponibilidadePorDataQuartoCommandHandler
 * @package Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto
 * @covers GetDisponibilidadePorDataQuartoCommandHandlerTest
 */
class GetDisponibilidadePorDataQuartoCommandHandler
{
    /**
     * @var DisponibilidadeRepositoryInterface
     */
    private $disponibilidade_repository;

    /**
     * GetDisponibilidadePorDataQuartoCommandHandler constructor.
     * @param DisponibilidadeRepositoryInterface $disponibilidade_repository
     */
    public function __construct(DisponibilidadeRepositoryInterface $disponibilidade_repository)
    {
        $this->disponibilidade_repository = $disponibilidade_repository;
    }

    /**
     * @param GetDisponibilidadePorDataQuartoCommand $command
     * @return Disponibilidade|null
     */
    public function handle(GetDisponibilidadePorDataQuartoCommand $command): ?Disponibilidade
    {
        return $this->disponibilidade_repository->findOneBy([
            'quarto' => $command->getQuarto(),
            'dia' => $command->getData()
        ]);
    }
}