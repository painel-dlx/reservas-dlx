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

namespace Reservas\Tests\UseCases\Pedidos\SalvarPedido;

use DateTime;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Tests\ReservasTestCase;
use Reservas\UseCases\Pedidos\SalvarPedido\SalvarPedidoCommand;
use Reservas\UseCases\Pedidos\SalvarPedido\SalvarPedidoCommandHandler;

/**
 * Class SalvarPedidoCommandHandlerTest
 * @package Reservas\UseCases\Pedidos\SalvarPedido
 * @coversDefaultClass \Reservas\UseCases\Pedidos\SalvarPedido\SalvarPedidoCommandHandler
 */
class SalvarPedidoCommandHandlerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return SalvarPedidoCommandHandler
     */
    public function test__construct(): SalvarPedidoCommandHandler
    {
        $container = self::$painel_dlx->getContainer();
        $handler = $container->get(SalvarPedidoCommandHandler::class);

        $this->assertInstanceOf(SalvarPedidoCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param SalvarPedidoCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_salvar_novo_Pedido(SalvarPedidoCommandHandler $handler)
    {
        $checkin = (new DateTime())->modify('+1 day');
        $checkout = (clone $checkin)->modify('+1 day');

        $command = new SalvarPedidoCommand(
            'Teste Unitário',
            '652.602.110-73',
            'teste@unitario.com',
            '(61) 9 8350-3517',
            12.34,
            [
                (object)[
                    'quartoID' => 1,
                    'quartoNome' => 'Quarto de Teste',
                    'checkin' => $checkin->format('Y-m-d'),
                    'checkout' => $checkout->format('Y-m-d'),
                    'adultos' => 1,
                    'criancas' => 0,
                    'valor' => 12.34
                ]
            ]
        );

        $pedido = $handler->handle($command);

        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertNotNull($pedido->getId());
    }
}
