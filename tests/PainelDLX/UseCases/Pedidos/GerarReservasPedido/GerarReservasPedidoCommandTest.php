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

namespace Reservas\PainelDLX\Tests\UseCases\Pedidos\GerarReservasPedido;

use Reservas\PainelDLX\Domain\Entities\Pedido;
use Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand;
use Reservas\Tests\ReservasTestCase;

/**
 * Class GerarReservasPedidoCommandTest
 * @package Reservas\PainelDLX\Tests\UseCases\Pedidos\GerarReservasPedido
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand
 */
class GerarReservasPedidoCommandTest extends ReservasTestCase
{
    /**
     * @return GerarReservasPedidoCommand
     */
    public function test__construct(): GerarReservasPedidoCommand
    {
        $pedido = new Pedido();
        $command = new GerarReservasPedidoCommand($pedido);

        $this->assertInstanceOf(GerarReservasPedidoCommand::class, $command);

        return $command;
    }

    /**
     * @param GerarReservasPedidoCommand $command
     * @covers ::getPedido
     * @depends test__construct
     */
    public function test_GetPedido(GerarReservasPedidoCommand $command)
    {
        $this->assertInstanceOf(Pedido::class, $command->getPedido());
    }
}
