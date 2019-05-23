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

namespace Reservas\Tests\UseCases\Emails\EnviarNotificacaoConfirmacaoPedido;

use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\UseCases\Emails\EnviarNotificacaoConfirmacaoPedido\EnviarNotificacaoConfirmacaoPedidoCommand;
use PHPUnit\Framework\TestCase;

/**
 * Class EnviarNotificacaoConfirmacaoPedidoCommandTest
 * @package Reservas\Tests\UseCases\Emails\EnviarNotificacaoConfirmacaoPedido
 * @coversDefaultClass \Reservas\UseCases\Emails\EnviarNotificacaoConfirmacaoPedido\EnviarNotificacaoConfirmacaoPedidoCommand
 */
class EnviarNotificacaoConfirmacaoPedidoCommandTest extends TestCase
{
    /**
     * @return EnviarNotificacaoConfirmacaoPedidoCommand
     */
    public function test__construct(): EnviarNotificacaoConfirmacaoPedidoCommand
    {
        $pedido = new Pedido();
        $command = new EnviarNotificacaoConfirmacaoPedidoCommand($pedido);

        $this->assertInstanceOf(EnviarNotificacaoConfirmacaoPedidoCommand::class, $command);

        return $command;
    }

    /**
     * @param EnviarNotificacaoConfirmacaoPedidoCommand $command
     * @covers ::getPedido
     * @depends test__construct
     */
    public function test_GetPedido(EnviarNotificacaoConfirmacaoPedidoCommand $command)
    {
        $this->assertInstanceOf(Pedido::class, $command->getPedido());
    }
}
