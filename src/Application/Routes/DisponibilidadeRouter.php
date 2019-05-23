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
use PainelDLX\Application\Middlewares\DefinePaginaMestra;
use PainelDLX\Application\Middlewares\VerificarLogon;
use PainelDLX\Application\Routes\PainelDLXRouter;
use Reservas\Presentation\PainelDLX\ApartHotel\Disponibilidade\Controllers\DisponPorPeriodoController;
use Reservas\Presentation\PainelDLX\ApartHotel\Disponibilidade\Controllers\MapaDisponController;

class DisponibilidadeRouter extends PainelDLXRouter
{
    /**
     * Registrar todas as rotas
     * @throws \Exception
     */
    public function registrar(): void
    {
        $router = $this->getRouter();

        $verificar_logon = new VerificarLogon($this->session);
        $define_pagina_mestra = new DefinePaginaMestra($this->painel_dlx->getServerRequest(), $this->session);

        $autorizacao_gerenciar_dispon = new Autorizacao('GERENCIAR_DISPONIBILIDADE');

        $router->get(
            '/painel-dlx/apart-hotel/disponibilidade',
            [MapaDisponController::class, 'calendario']
        )->middlewares(
            $verificar_logon,
            $autorizacao_gerenciar_dispon,
            $define_pagina_mestra
        );

        $router->post(
            '/painel-dlx/apart-hotel/disponibilidade/salvar',
            [MapaDisponController::class, 'salvar']
        )->middlewares(
            $verificar_logon,
            $autorizacao_gerenciar_dispon
        );

        $router->get(
            '/painel-dlx/apart-hotel/disponibilidade/editar-por-periodo',
            [DisponPorPeriodoController::class, 'formDisponPorPeriodo']
        )->middlewares(
            $verificar_logon,
            $autorizacao_gerenciar_dispon,
            $define_pagina_mestra
        );

        $router->get(
            '/painel-dlx/apart-hotel/disponibilidade/configuracoes-quarto',
            [DisponPorPeriodoController::class, 'disponConfigQuarto']
        )->middlewares(
            $verificar_logon,
            $autorizacao_gerenciar_dispon,
            $define_pagina_mestra
        );

        $router->post(
            '/painel-dlx/apart-hotel/disponibilidade/salvar-periodo',
            [DisponPorPeriodoController::class, 'salvarDisponPorPeriodo']
        )->middlewares(
            $verificar_logon,
            $autorizacao_gerenciar_dispon
        );
    }
}