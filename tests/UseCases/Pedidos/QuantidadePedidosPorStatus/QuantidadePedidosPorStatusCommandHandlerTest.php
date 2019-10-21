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

namespace Reservas\Tests\UseCases\Pedidos\QuantidadePedidosPorStatus;

use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;
use Reservas\UseCases\Pedidos\QuantidadePedidosPorStatus\QuantidadePedidosPorStatusCommand;
use Reservas\UseCases\Pedidos\QuantidadePedidosPorStatus\QuantidadePedidosPorStatusCommandHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class QuantidadePedidosPorStatusCommandHandlerTest
 * @package Reservas\Tests\UseCases\Pedidos\QuantidadePedidosPorStatus
 * @coversDefaultClass \Reservas\UseCases\Pedidos\QuantidadePedidosPorStatus\QuantidadePedidosPorStatusCommandHandler
 */
class QuantidadePedidosPorStatusCommandHandlerTest extends TestCase
{
    /**
     * @return array
     */
    public function providerStatus(): array
    {
        return [
            ['pendente', 2],
            ['cancelado', 234],
            ['pago', 345]
        ];
    }

    /**
     * @covers ::handle
     * @dataProvider providerStatus
     * @param string $status
     * @param int $quantidade
     */
    public function test_Handle_deve_retornar_quantidade_de_pedidos_em_determinado_status(string $status, int $quantidade)
    {
        $pedido_repository = $this->createMock(PedidoRepositoryInterface::class);
        $pedido_repository->method('getQuantidadePedidosPorStatus')->willReturn($quantidade);

        /** @var PedidoRepositoryInterface $pedido_repository */

        $command = new QuantidadePedidosPorStatusCommand($status, null, null);
        $handler = new QuantidadePedidosPorStatusCommandHandler($pedido_repository);
        $quantidade_pedidos = $handler->handle($command);

        $this->assertEquals($quantidade, $quantidade_pedidos);
    }
}
