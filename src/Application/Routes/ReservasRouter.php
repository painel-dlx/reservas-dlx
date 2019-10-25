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
use Reservas\Presentation\PainelDLX\ApartHotel\Reservas\Controllers\DetalheReservaController;
use Reservas\Presentation\PainelDLX\ApartHotel\Reservas\Controllers\ListaReservasController;
use Reservas\Presentation\PainelDLX\ApartHotel\Reservas\Controllers\SalvarReservaController;

class ReservasRouter extends PainelDLXRouter
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
        /** @var Autorizacao $autorizacao */
        $autorizacao = $container->get(Autorizacao::class);

        $autorizacao_reservar_quarto = $autorizacao->necessitaPermissoes('RESERVAR_QUARTOS');
        $autorizacao_listar_reservas = $autorizacao->necessitaPermissoes('VER_LISTA_RESERVAS');
        $autorizacao_detalhe_reservas = $autorizacao->necessitaPermissoes('VER_DETALHE_RESERVAS');
        $autorizacao_confirmar_reserva = $autorizacao->necessitaPermissoes('CONFIRMAR_RESERVAS');
        $autorizacao_cancelar_reserva = $autorizacao->necessitaPermissoes('CANCELAR_RESERVAS');

        $router->get(
            '/painel-dlx/apart-hotel/reservas',
            [ListaReservasController::class, 'listaReservas']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon,
            $autorizacao_listar_reservas,
            $paginacao
        );

        $router->get(
            '/painel-dlx/apart-hotel/reservas/reservar-quarto',
            [SalvarReservaController::class, 'formReservarQuarto']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon,
            $autorizacao_reservar_quarto
        );

        $router->post(
            '/painel-dlx/apart-hotel/reservas/salvar-reserva',
            [SalvarReservaController::class, 'criarReserva']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon,
            $autorizacao_reservar_quarto
        );

        $router->get(
            '/painel-dlx/apart-hotel/reservas/detalhe',
            [DetalheReservaController::class, 'detalhesReserva']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon,
            $autorizacao_detalhe_reservas
        );

        $router->get(
            '/painel-dlx/apart-hotel/reservas/mostrar-cpf-completo',
            [DetalheReservaController::class, 'mostrarCpfCompleto']
        )->middlewares(
            $verificar_logon,
            $autorizacao_detalhe_reservas
        );

        $router->get(
            '/painel-dlx/apart-hotel/reservas/confirmar-reserva',
            [DetalheReservaController::class, 'formConfirmarReserva']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon,
            $autorizacao_confirmar_reserva
        );

        $router->post(
            '/painel-dlx/apart-hotel/reservas/confirmar-reserva',
            [DetalheReservaController::class, 'confirmarReserva']
        )->middlewares(
            $verificar_logon,
            $autorizacao_confirmar_reserva
        );

        $router->get(
            '/painel-dlx/apart-hotel/reservas/cancelar-reserva',
            [DetalheReservaController::class, 'formCancelarReserva']
        )->middlewares(
            $define_pagina_mestra,
            $verificar_logon,
            $autorizacao_cancelar_reserva
        );

        $router->post(
            '/painel-dlx/apart-hotel/reservas/cancelar-reserva',
            [DetalheReservaController::class, 'cancelarReserva']
        )->middlewares(
            $verificar_logon,
            $autorizacao_cancelar_reserva
        );
    }
}