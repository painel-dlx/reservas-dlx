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

namespace Reservas\PainelDLX\Application\Routes;


use PainelDLX\Application\Middlewares\DefinePaginaMestra;
use PainelDLX\Application\Middlewares\VerificarLogon;
use PainelDLX\Application\Routes\PainelDLXRouter;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\DetalhePedidoController;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\ListaPedidosController;

class PedidosRouter extends PainelDLXRouter
{

    /**
     * Registrar todas as rotas
     */
    public function registrar(): void
    {
        $router = $this->getRouter();

        $verificar_logon = new VerificarLogon($this->session);
        $define_pagina_mestra = new DefinePaginaMestra($this->painel_dlx->getServerRequest(), $this->session);

        $router->get(
            '/painel-dlx/apart-hotel/pedidos',
            [ListaPedidosController::class, 'listaPedidos']
        )->middlewares(
            $verificar_logon,
            $define_pagina_mestra
        );

        $router->get(
            '/painel-dlx/apart-hotel/pedidos/detalhe',
            [DetalhePedidoController::class, 'detalhePedido']
        )->middlewares(
            $verificar_logon,
            $define_pagina_mestra
        );

        $router->post(
            '/painel-dlx/apart-hotel/pedidos/confirmar-pedido',
            [DetalhePedidoController::class, 'confirmarPgtoPedido']
        )->middlewares(
            $verificar_logon,
            $define_pagina_mestra
        );

        $router->get(
            '/painel-dlx/apart-hotel/pedidos/mostrar-cpf-completo',
            [DetalhePedidoController::class, 'mostrarCpfCompleto']
        )->middlewares(
            $verificar_logon,
            $define_pagina_mestra
        );
    }
}