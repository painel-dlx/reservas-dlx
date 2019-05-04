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

namespace Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto;


use Reservas\PainelDLX\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;

/**
 * Class SalvarDisponibilidadeQuartoCommandHandler
 * @package Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto
 */
class SalvarDisponibilidadeQuartoCommandHandler
{
    /**
     * @var \Reservas\PainelDLX\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface
     */
    private $disponibilidade_repository;

    /**
     * SalvarDisponibilidadeQuartoCommandHandler constructor.
     * @param \Reservas\PainelDLX\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface $disponibilidade_repository
     */
    public function __construct(DisponibilidadeRepositoryInterface $disponibilidade_repository)
    {
        $this->disponibilidade_repository = $disponibilidade_repository;
    }

    /**
     * @param SalvarDisponibilidadeQuartoCommand $command
     * @return Disponibilidade
     */
    public function handle(SalvarDisponibilidadeQuartoCommand $command): Disponibilidade
    {
        $disponibilidade = $command->getDisponibilidade();

        if (is_null($disponibilidade->getId())) {
            $this->disponibilidade_repository->create($disponibilidade);
        } else {
            $this->disponibilidade_repository->update($disponibilidade);
        }

        return $disponibilidade;
    }
}