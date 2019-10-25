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

namespace Reservas\Tests\UseCases\Quartos\GetQuartoPorId;

use DLX\Infra\EntityManagerX;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Exceptions\QuartoNaoEncontradoException;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Tests\ReservasTestCase;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommandHandler;

/**
 * Class GetQuartoPorIdCommandHandlerTest
 * @package Reservas\Tests\UseCases\Quartos\GetQuartoPorId
 * @coversDefaultClass  \Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommandHandler
 */
class GetQuartoPorIdCommandHandlerTest extends ReservasTestCase
{
    /**
     * @throws QuartoNaoEncontradoException
     * @covers ::handle
     */
    public function test_Handle_deve_lancar_excecao_quando_nao_encontrar_registro_no_bd()
    {
        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('find')->willReturn(null);

        /** @var QuartoRepositoryInterface $quarto_repository */

        $this->expectException(QuartoNaoEncontradoException::class);
        $this->expectExceptionCode(10);

        $command = new GetQuartoPorIdCommand(0);
        (new GetQuartoPorIdCommandHandler($quarto_repository))->handle($command);
    }

    /**
     * @throws QuartoNaoEncontradoException
     * @covers ::handle
     */
    public function test_Handle_deve_retornar_Quarto_quando_encontrar_registro_no_bd()
    {
        $quarto_id = mt_rand();

        $quarto = $this->createMock(Quarto::class);
        $quarto->method('getId')->willReturn($quarto_id);

        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('find')->willReturn($quarto);

        /** @var QuartoRepositoryInterface $quarto_repository */

        $command = new GetQuartoPorIdCommand($quarto_id);
        $quarto_retornado = (new GetQuartoPorIdCommandHandler($quarto_repository))->handle($command);

        $this->assertInstanceOf(Quarto::class, $quarto_retornado);
        $this->assertEquals($quarto_id, $quarto_retornado->getId());
    }
}
