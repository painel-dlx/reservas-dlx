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

namespace Reservas\Tests\UseCases\Pedidos\GetPedidoPorId;

use PHPUnit\Framework\TestCase;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Exceptions\PedidoNaoEncontradoException;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommand;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommandHandler;

/**
 * Class GetPedidoPorIdCommandHandlerTest
 * @package Reservas\Tests\UseCases\Pedidos\GetPedidoPorId
 * @coversDefaultClass \Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommandHandler
 */
class GetPedidoPorIdCommandHandlerTest extends TestCase
{
    /**
     * @covers ::handle
     * @throws PedidoNaoEncontradoException
     */
    public function test_Handle_deve_lancar_excecao_quando_nao_encontrar_registro_no_bd()
    {
        $pedido_repository = $this->createMock(PedidoRepositoryInterface::class);
        $pedido_repository->method('find')->willReturn(null);

        /** @var PedidoRepositoryInterface $pedido_repository */

        $this->expectException(PedidoNaoEncontradoException::class);
        $this->expectExceptionCode(10);

        $command = new GetPedidoPorIdCommand(0);
        (new GetPedidoPorIdCommandHandler($pedido_repository))->handle($command);
    }

    /**
     * @covers ::handle
     * @throws PedidoNaoEncontradoException
     */
    public function test_Handle_deve_retornar_Pedido_quando_encontrar_registro_no_bd()
    {
        $pedido_id = mt_rand();

        $pedido = $this->createMock(Pedido::class);
        $pedido->method('getId')->willReturn($pedido_id);

        $pedido_repository = $this->createMock(PedidoRepositoryInterface::class);
        $pedido_repository->method('find')->willReturn($pedido);

        /** @var PedidoRepositoryInterface $pedido_repository */

        $command = new GetPedidoPorIdCommand($pedido_id);
        $pedido_retornado = (new GetPedidoPorIdCommandHandler($pedido_repository))->handle($command);

        $this->assertInstanceOf(Pedido::class, $pedido_retornado);
        $this->assertEquals($pedido_id, $pedido_retornado->getId());
    }
}
