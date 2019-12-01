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

namespace Reservas\Tests\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto;

use DateTime;
use DLX\Infrastructure\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Exception;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Tests\ReservasTestCase;
use Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand;
use Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommandHandler;

/**
 * Class GetDisponibilidadePorDataQuartoCommandHandlerTest
 * @package Reservas\Tests\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto
 * @coversDefaultClass \Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommandHandler
 */
class GetDisponibilidadePorDataQuartoCommandHandlerTest extends ReservasTestCase
{
    /**
     * @throws Exception
     * @covers ::handle
     */
    public function test_Handle_deve_retornar_null_quando_nao_encontrar_registro()
    {
        $disponibilidade_repository = $this->createMock(DisponibilidadeRepositoryInterface::class);
        $disponibilidade_repository->method('findOneBy')->willReturn(null);
        $quarto = $this->createMock(Quarto::class);
        $data = new DateTime();

        /** @var DisponibilidadeRepositoryInterface $disponibilidade_repository */
        /** @var Quarto $quarto */

        $handler = new GetDisponibilidadePorDataQuartoCommandHandler($disponibilidade_repository);
        $command = new GetDisponibilidadePorDataQuartoCommand($quarto, $data);
        $disponibilidade = $handler->handle($command);

        $this->assertNull($disponibilidade);
    }

    /**
     * @throws Exception
     * @covers ::handle
     */
    public function test_Handle_deve_retornar_Disponibilidade_quando_encontrar_registro()
    {
        $disponibilidade = $this->createMock(Disponibilidade::class);

        $disponibilidade_repository = $this->createMock(DisponibilidadeRepositoryInterface::class);
        $disponibilidade_repository->method('findOneBy')->willReturn($disponibilidade);

        $quarto = $this->createMock(Quarto::class);
        $data = new DateTime();

        /** @var DisponibilidadeRepositoryInterface $disponibilidade_repository */
        /** @var Quarto $quarto */

        $handler = new GetDisponibilidadePorDataQuartoCommandHandler($disponibilidade_repository);
        $command = new GetDisponibilidadePorDataQuartoCommand($quarto, $data);
        $disponibilidade = $handler->handle($command);

        $this->assertInstanceOf(Disponibilidade::class, $disponibilidade);
    }
}
