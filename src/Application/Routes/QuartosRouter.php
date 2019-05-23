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
use Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Controllers\GerenciadorMidiasController;
use Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Controllers\CadastrarQuartoController;
use Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Controllers\EditarQuartoController;
use Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Controllers\ListaQuartosController;

class QuartosRouter extends PainelDLXRouter
{

    /**
     * Registrar todas as rotas
     * @throws \Exception
     */
    public function registrar(): void
    {
        $router = $this->getRouter();

        // Middlewares
        $verificar_logon = new VerificarLogon($this->session);
        $define_pagina_mestra = new DefinePaginaMestra($this->painel_dlx->getServerRequest(), $this->session);

        // Permissões
        $perm_cadastrar_quarto = new Autorizacao('CADASTRAR_NOVO_QUARTO');
        $perm_editar_quarto = new Autorizacao('EDITAR_QUARTO');

        $router->get(
            '/painel-dlx/apart-hotel/quartos',
            [ListaQuartosController::class, 'listaQuartos']
        )->middlewares(
            $verificar_logon,
            new Autorizacao('VER_LISTA_QUARTOS'),
            $define_pagina_mestra
        );

        $router->post(
            '/painel-dlx/apart-hotel/quartos/excluir',
            [ListaQuartosController::class, 'excluirQuarto']
        )->middlewares(
            $verificar_logon,
            new Autorizacao('EXCLUIR_QUARTO')
        );

        $router->get(
            '/painel-dlx/apart-hotel/quartos/novo',
            [CadastrarQuartoController::class, 'formNovoQuarto']
        )->middlewares(
            $verificar_logon,
            $perm_cadastrar_quarto,
            $define_pagina_mestra
        );

        $router->post(
            '/painel-dlx/apart-hotel/quartos/salvar-novo',
            [CadastrarQuartoController::class, 'salvarNovoQuarto']
        )->middlewares(
            $verificar_logon,
            $perm_cadastrar_quarto
        );

        $router->get(
            '/painel-dlx/apart-hotel/quartos/editar',
            [EditarQuartoController::class, 'formEditarQuarto']
        )->middlewares(
            $verificar_logon,
            $perm_editar_quarto,
            $define_pagina_mestra
        );

        $router->post(
            '/painel-dlx/apart-hotel/quartos/atualizar-informacoes',
            [EditarQuartoController::class, 'editarInformacoesQuarto']
        )->middlewares(
            $verificar_logon,
            $perm_editar_quarto
        );

        $router->get(
            '/painel-dlx/apart-hotel/quartos/upload-midias',
            [GerenciadorMidiasController::class, 'formUpload']
        )->middlewares(
            $verificar_logon,
            $perm_editar_quarto,
            $define_pagina_mestra
        );

        $router->post(
            '/painel-dlx/apart-hotel/quartos/upload-midias',
            [GerenciadorMidiasController::class, 'uploadMidias']
        )->middlewares(
            $verificar_logon,
            $perm_editar_quarto
        );
    }
}