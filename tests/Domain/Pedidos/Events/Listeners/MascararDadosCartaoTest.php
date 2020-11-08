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

namespace Reservas\Tests\Domain\Pedidos\Events\Listeners;

use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Entities\PedidoCartao;
use Reservas\Domain\Pedidos\Events\Common\PedidoEvent;
use Reservas\Domain\Pedidos\Events\Listeners\MascararDadosCartao;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;
use Reservas\Tests\ReservasTestCase;

/**
 * Class MascararDadosCartaoTest
 * @package MascararDadosCartao
 * @coversDefaultClass \Reservas\Domain\Pedidos\Events\Listeners\MascararDadosCartao
 */
class MascararDadosCartaoTest extends ReservasTestCase
{
    /**
     * @test
     * @covers ::handle
     */
    public function deve_manter_os_dados_cartao_quando_o_pedido_ainda_estiver_pendente()
    {
        $pedido = $this->createMock(Pedido::class);
        $pedido->method('isPendente')->willReturn(true);
        $pedido->method('getCartao')->willReturn(
            /** @var Pedido $pedido */
            new PedidoCartao(
                $pedido,
                'Diego Lepera',
                '1234-5678-9012-3456',
                '10/2029',
                '123'
            )
        );

        $pedido_repository = $this->createMock(PedidoRepositoryInterface::class);

        $pedido_event = $this->createMock(PedidoEvent::class);
        $pedido_event->method('getPedido')->willReturn($pedido);

        /** @var PedidoRepositoryInterface $pedido_repository */
        /** @var PedidoEvent $pedido_event */

        $mascararDadosCartao = new MascararDadosCartao($pedido_repository);
        $mascararDadosCartao->handle($pedido_event);

        $pedido_cartao = $pedido->getCartao();

        $this->assertRegExp('~^[0-9]{3}$~', $pedido_cartao->getCodigoSeguranca());
        $this->assertRegExp('~^([0-9]{4}-){3}[0-9]{4}~', $pedido_cartao->getNumeroCartao());
    }

    /**
     * @test
     * @covers ::handle
     */
    public function deve_mascarar_as_informacoes_criticas_do_cartao()
    {
        $pedido = $this->createMock(Pedido::class);
        $pedido->method('isPendente')->willReturn(false);
        $pedido->method('getCartao')->willReturn(
        /** @var Pedido $pedido */
            new PedidoCartao(
                $pedido,
                'Diego Lepera',
                '1234-5678-9012-3456',
                '10/2029',
                '123'
            )
        );

        $pedido_repository = $this->createMock(PedidoRepositoryInterface::class);

        $pedido_event = $this->createMock(PedidoEvent::class);
        $pedido_event->method('getPedido')->willReturn($pedido);

        /** @var PedidoRepositoryInterface $pedido_repository */
        /** @var PedidoEvent $pedido_event */

        $mascararDadosCartao = new MascararDadosCartao($pedido_repository);
        $mascararDadosCartao->handle($pedido_event);

        $pedido_cartao = $pedido->getCartao();

        $this->assertEquals('***', $pedido_cartao->getCodigoSeguranca());
        $this->assertRegExp('~^(\*{4}-){3}[0-9]{4}~', $pedido_cartao->getNumeroCartao());
    }
}
