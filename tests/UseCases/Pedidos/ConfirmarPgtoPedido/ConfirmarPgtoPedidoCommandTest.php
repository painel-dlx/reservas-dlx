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

namespace Reservas\Tests\UseCases\Pedidos\ConfirmarPgtoPedido;

use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommand;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfirmarPgtoPedidoCommandTest
 * @package Reservas\Tests\UseCases\Pedidos\ConfirmarPgtoPedido
 * @coversDefaultClass \Reservas\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommand
 */
class ConfirmarPgtoPedidoCommandTest extends TestCase
{
    /**
     * @return ConfirmarPgtoPedidoCommand
     */
    public function test__construct(): ConfirmarPgtoPedidoCommand
    {
        $pedido = new Pedido();
        $motivo = 'Teste de motivo para confirmar um pedido';
        $usuario = new Usuario('Funcionário', 'func@gmail.com');

        $command = new ConfirmarPgtoPedidoCommand($pedido, $motivo, $usuario);

        $this->assertInstanceOf(ConfirmarPgtoPedidoCommand::class, $command);

        return $command;
    }

    /**
     * @param ConfirmarPgtoPedidoCommand $command
     * @covers ::getPedido
     * @depends test__construct
     */
    public function test_GetPedido(ConfirmarPgtoPedidoCommand $command)
    {
        $this->assertInstanceOf(Pedido::class, $command->getPedido());
    }

    /**
     * @param ConfirmarPgtoPedidoCommand $command
     * @covers ::getMotivo
     * @depends test__construct
     */
    public function test_GetMotivo(ConfirmarPgtoPedidoCommand $command)
    {
        $this->assertIsString($command->getMotivo());
    }

    /**
     * @param ConfirmarPgtoPedidoCommand $command
     * @covers ::getUsuario
     * @depends test__construct
     */
    public function test_GetUsuario(ConfirmarPgtoPedidoCommand $command)
    {
        $this->assertInstanceOf(Usuario::class, $command->getUsuario());
    }
}
