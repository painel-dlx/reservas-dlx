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

namespace Reservas\Application\Routes;


use PainelDLX\Application\Middlewares\Autorizacao;
use PainelDLX\Application\Middlewares\ConfigurarPaginacao;
use PainelDLX\Application\Middlewares\DefinePaginaMestra;
use PainelDLX\Application\Middlewares\VerificarLogon;
use PainelDLX\Application\Routes\PainelDLXRouter;
use PainelDLX\Application\Services\PainelDLX;
use Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers\DetalhePedidoController;
use Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers\ListaPedidosController;
use Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers\WidgetPedidoController;

class PedidosRouter extends PainelDLXRouter
{
    /**
     * Registrar todas as rotas
     */
    public function registrar(): void
    {
        $router = $this->getRouter();
        $container = PainelDLX::getInstance()->getContainer();

        /** @var VerificarLogon $verificar_logon */
        $verificar_logon = $container->get(VerificarLogon::class);
        /** @var DefinePaginaMestra $define_pagina_mestra */
        $define_pagina_mestra = $container->get(DefinePaginaMestra::class);
        /** @var ConfigurarPaginacao $paginacao */
        $paginacao = $container->get(ConfigurarPaginacao::class);

        $router->get(
            '/painel-dlx/apart-hotel/pedidos-{status}',
            [ListaPedidosController::class, 'listaPedidos']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon,
            $paginacao
        );

        $router->get(
            '/painel-dlx/apart-hotel/pedidos/detalhe',
            [DetalhePedidoController::class, 'detalhePedido']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon
        );

        $router->get(
            '/painel-dlx/apart-hotel/pedidos/detalhe/detalhamento-periodo',
            [DetalhePedidoController::class, 'detalhamentoPeriodo']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon
        );

        $router->get(
            '/painel-dlx/apart-hotel/pedidos/mostrar-cpf-completo',
            [DetalhePedidoController::class, 'mostrarCpfCompleto']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon
        );

        $router->get(
            '/painel-dlx/apart-hotel/pedidos/confirmar-pedido',
            [DetalhePedidoController::class, 'formConfirmarPgtoPedido']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon
        );

        $router->post(
            '/painel-dlx/apart-hotel/pedidos/confirmar-pedido',
            [DetalhePedidoController::class, 'confirmarPgtoPedido']
        )->middlewares(
            $verificar_logon
        );

        $router->get(
            '/painel-dlx/apart-hotel/pedidos/cancelar-pedido',
            [DetalhePedidoController::class, 'formCancelarPedido']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon
        );

        $router->post(
            '/painel-dlx/apart-hotel/pedidos/cancelar-pedido',
            [DetalhePedidoController::class, 'cancelarPedido']
        )->middlewares(
            $verificar_logon
        );

        $router->get(
            '/painel-dlx/apart-hotel/pedidos/quantidade-pedidos-{status}',
            [WidgetPedidoController::class, 'quantidadePedidosPorStatus']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon
        );
    }
}