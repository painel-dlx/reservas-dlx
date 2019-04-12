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

namespace Reservas\Website\Tests\UseCases\FindQuartosDisponiveis;

use DateTime;
use DLX\Infra\EntityManagerX;
use Reservas\Tests\ReservasTestCase;
use Reservas\Website\Domain\Entities\Disponibilidade;
use Reservas\Website\Domain\Entities\Quarto;
use Reservas\Website\Domain\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\Website\Domain\Repositories\QuartoRepositoryInterface;
use Reservas\Website\UseCases\FindQuartosDisponiveis\FindQuartosDisponiveisCommand;
use Reservas\Website\UseCases\FindQuartosDisponiveis\FindQuartosDisponiveisCommandHandler;

/**
 * Class FindQuartosDisponiveisCommandHandlerTest
 * @package Reservas\Website\Tests\UseCases\FindQuartosDisponiveis
 * @coversDefaultClass \Reservas\Website\UseCases\FindQuartosDisponiveis\FindQuartosDisponiveisCommandHandler
 */
class FindQuartosDisponiveisCommandHandlerTest extends ReservasTestCase
{

    /**
     * @return FindQuartosDisponiveisCommandHandler
     * @throws \Doctrine\ORM\ORMException
     */
    public function test__construct()
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);

        $handler = new FindQuartosDisponiveisCommandHandler($quarto_repository);
        $this->assertInstanceOf(FindQuartosDisponiveisCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param FindQuartosDisponiveisCommandHandler $handler
     * @throws \Exception
     * @depends test__construct
     */
    public function test_Handle_deve_retornar_array_com_quartos(FindQuartosDisponiveisCommandHandler $handler)
    {
        $data_entrada = new DateTime();
        $data_saida = (new DateTime())->modify('+3 days');
        $qtde_adultos = mt_rand(1, 5);
        $qtde_criancas = mt_rand(0, 3);
        $qtde_quartos = 1;

        $command = new FindQuartosDisponiveisCommand($data_entrada, $data_saida, $qtde_adultos, $qtde_criancas, $qtde_quartos);
        $lista_quartos = $handler->handle($command);

        $this->assertIsArray($lista_quartos);
    }
}
