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


use PainelDLX\Application\Middlewares\Autorizacao;
use PainelDLX\Application\Middlewares\DefinePaginaMestra;
use PainelDLX\Application\Middlewares\VerificarLogon;
use PainelDLX\Application\Routes\PainelDLXRouter;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\DetalheReservaController;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\ListaReservasController;

class ReservasRouter extends PainelDLXRouter
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
            '/painel-dlx/apart-hotel/reservas',
            [ListaReservasController::class, 'listaReservas']
        )->middlewares(
            $verificar_logon,
            new Autorizacao('VER_LISTA_RESERVAS'),
            $define_pagina_mestra
        );

        $router->get(
            '/painel-dlx/apart-hotel/reservas/detalhe',
            [ListaReservasController::class, 'detalhesReserva']
        )->middlewares(
            $verificar_logon,
            new Autorizacao('VER_DETALHES_RESERVAS'),
            $define_pagina_mestra
        );


        $router->get(
            '/painel-dlx/apart-hotel/reservas/confirmar-reserva',
            [DetalheReservaController::class, 'formConfirmarReserva']
        )->middlewares(
            $verificar_logon,
            new Autorizacao('CONFIRMAR_RESERVAS'),
            $define_pagina_mestra
        );

        $router->post(
            '/painel-dlx/apart-hotel/reservas/confirmar-reserva',
            [DetalheReservaController::class, 'confirmarReserva']
        )->middlewares(
            $verificar_logon,
            new Autorizacao('CONFIRMAR_RESERVAS')
        );

        $router->get(
            '/painel-dlx/apart-hotel/reservas/cancelar-reserva',
            [DetalheReservaController::class, 'formCancelarReserva']
        )->middlewares(
            $verificar_logon,
            new Autorizacao('CANCELAR_RESERVAS'),
            $define_pagina_mestra
        );

        $router->post(
            '/painel-dlx/apart-hotel/reservas/cancelar-reserva',
            [DetalheReservaController::class, 'cancelarReserva']
        )->middlewares(
            $verificar_logon,
            new Autorizacao('CANCELAR_RESERVAS')
        );
    }
}