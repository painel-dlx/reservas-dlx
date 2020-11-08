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

namespace Reservas\Tests\UseCases\Disponibilidade\SalvarDisponPeriodo;

use DateTime;
use DLX\Infrastructure\EntityManagerX;
use Doctrine\ORM\ORMException;
use Exception;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommand;
use Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class SalvarDisponPeriodoCommandHandlerTest
 * @package Reservas\Tests\UseCases\Disponibilidade\SalvarDisponPeriodo
 * @coversDefaultClass \Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommandHandler
 */
class SalvarDisponPeriodoCommandHandlerTest extends ReservasTestCase
{
    /**
     * @return SalvarDisponPeriodoCommandHandler
     * @throws ORMException
     */
    public function test__construct(): SalvarDisponPeriodoCommandHandler
    {
        /** @var DisponibilidadeRepositoryInterface $disponibilidade_repository */
        $disponibilidade_repository = EntityManagerX::getRepository(Disponibilidade::class);

        $handler = new SalvarDisponPeriodoCommandHandler($disponibilidade_repository);
        $this->assertInstanceOf(SalvarDisponPeriodoCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param SalvarDisponPeriodoCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     * @throws Exception
     */
    public function test_Handle(SalvarDisponPeriodoCommandHandler $handler)
    {
        $data_inicial = new DateTime();
        $data_final = (new DateTime())->modify('+2 days');

        $quarto = new Quarto('Teste', 1, 10);
        $quarto->setId(1);

        $command = new SalvarDisponPeriodoCommand(
            $data_inicial,
            $data_final,
            $quarto,
            1,
            [1 => 12.34],
            null
        );
        $return = $handler->handle($command);

        $this->assertIsBool($return);
    }
}
