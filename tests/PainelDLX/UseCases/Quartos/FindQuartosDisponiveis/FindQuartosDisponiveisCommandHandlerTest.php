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

namespace Reservas\PainelDLX\Tests\UseCases\Quartos\FindQuartosDisponiveis;

use DLX\Infra\EntityManagerX;
use Doctrine\ORM\ORMException;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\PainelDLX\UseCases\Quartos\FindQuartosDisponiveis\FindQuartosDisponiveisCommand;
use Reservas\PainelDLX\UseCases\Quartos\FindQuartosDisponiveis\FindQuartosDisponiveisCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class FindQuartosDisponiveisCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Quartos\FindQuartosDisponiveis
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Quartos\FindQuartosDisponiveis\FindQuartosDisponiveisCommandHandler
 */
class FindQuartosDisponiveisCommandHandlerTest extends ReservasTestCase
{
    /**
     * @return FindQuartosDisponiveisCommandHandler
     * @throws ORMException
     */
    public function test__construct(): FindQuartosDisponiveisCommandHandler
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);
        $handler = new FindQuartosDisponiveisCommandHandler($quarto_repository);

        $this->assertInstanceOf(FindQuartosDisponiveisCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param FindQuartosDisponiveisCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_retornar_um_array_com_Quartos_disponiveis(FindQuartosDisponiveisCommandHandler $handler)
    {
        $checkin = new \DateTime();
        $checkout = (clone $checkin)->modify('+2 days');
        $command = new FindQuartosDisponiveisCommand($checkin, $checkout, 1, 1);

        $lista_quartos = $handler->handle($command);

        $this->assertIsArray($lista_quartos);
    }
}
