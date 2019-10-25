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

namespace Reservas\Tests\UseCases\Quartos\GetQuartoPorLink;

use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Exceptions\QuartoNaoEncontradoException;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\UseCases\Quartos\GetQuartoPorLink\GetQuartoPorLinkCommand;
use Reservas\UseCases\Quartos\GetQuartoPorLink\GetQuartoPorLinkCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class GetQuartoPorLinkCommandHandlerTest
 * @package Reservas\Tests\UseCases\Quartos\GetQuartoPorLink
 * @coversDefaultClass \Reservas\UseCases\Quartos\GetQuartoPorLink\GetQuartoPorLinkCommandHandler
 */
class GetQuartoPorLinkCommandHandlerTest extends ReservasTestCase
{
    /**
     * @covers ::handle
     * @throws QuartoNaoEncontradoException
     */
    public function test_Handle_deve_lancar_excecao_quando_nao_encontrar_registro_no_bd()
    {
        $link = 'nossos-quartos/teste-quarto-unitario';

        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('findOneBy')->willReturn(null);

        /** @var QuartoRepositoryInterface $quarto_repository */

        $this->expectException(QuartoNaoEncontradoException::class);
        $this->expectExceptionCode(11);

        $command = new GetQuartoPorLinkCommand($link);
        (new GetQuartoPorLinkCommandHandler($quarto_repository))->handle($command);
    }

    /**
     * @throws QuartoNaoEncontradoException
     * @covers ::handle
     */
    public function test_Handle_deve_retornar_Quarto_quando_encontrar_registro_no_bd()
    {
        $quarto_id = mt_rand();
        $link = 'nossos-quartos/teste-quarto-unitario';

        $quarto = $this->createMock(Quarto::class);
        $quarto->method('getId')->willReturn($quarto_id);
        $quarto->method('getLink')->willReturn($link);

        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('findOneBy')->willReturn($quarto);

        /** @var QuartoRepositoryInterface $quarto_repository */

        $command = new GetQuartoPorLinkCommand($link);
        $quarto_retornado = (new GetQuartoPorLinkCommandHandler($quarto_repository))->handle($command);

        $this->assertInstanceOf(Quarto::class, $quarto_retornado);
        $this->assertEquals($link, $quarto_retornado->getLink());
        $this->assertEquals($quarto_id, $quarto_retornado->getId());
    }
}
