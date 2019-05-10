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

namespace Reservas\PainelDLX\UseCases\Quartos\GerarDisponibilidadesQuarto;


use DateTime;
use Reservas\PainelDLX\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\PainelDLX\Domain\Quartos\Repositories\QuartoRepositoryInterface;

/**
 * Class GerarDisponibilidadesQuartoCommandHandler
 * @package Reservas\PainelDLX\UseCases\Quartos\GerarDisponibilidadesQuarto
 * @covers GerarDisponibilidadesQuartoCommandHandlerTest
 */
class GerarDisponibilidadesQuartoCommandHandler
{
    /**
     * @var QuartoRepositoryInterface
     */
    private $quarto_repository;
    /**
     * @var DisponibilidadeRepositoryInterface
     */
    private $dispon_repository;

    /**
     * GerarDisponibilidadesQuartoCommandHandler constructor.
     * @param QuartoRepositoryInterface $quarto_repository
     * @param DisponibilidadeRepositoryInterface $dispon_repository
     */
    public function __construct(
        QuartoRepositoryInterface $quarto_repository,
        DisponibilidadeRepositoryInterface $dispon_repository
    ) {
        $this->quarto_repository = $quarto_repository;
        $this->dispon_repository = $dispon_repository;
    }

    /**
     * @param GerarDisponibilidadesQuartoCommand $command
     */
    public function handle(GerarDisponibilidadesQuartoCommand $command)
    {
        $dt_inicial = new DateTime();
        $dt_final = $this->dispon_repository->ultimaDataDisponibilidade();

        $this->quarto_repository->gerarDisponibilidadesQuarto(
            $command->getQuarto(),
            $dt_inicial,
            $dt_final
        );
    }
}