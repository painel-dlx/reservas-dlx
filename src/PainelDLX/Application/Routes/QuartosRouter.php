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
use PainelDLX\Application\Middlewares\VerificarLogon;
use PainelDLX\Application\Routes\PainelDLXRouter;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\CadastrarQuartoController;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\EditarQuartoController;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\ListaQuartosController;

class QuartosRouter extends PainelDLXRouter
{

    /**
     * Registrar todas as rotas
     * @throws \Exception
     */
    public function registrar(): void
    {
        $router = $this->getRouter();

        $router->get(
            '/painel-dlx/apart-hotel/quartos',
            [ListaQuartosController::class, 'listaQuartos']
        )->middlewares(
            new VerificarLogon($this->session),
            new Autorizacao('VER_LISTA_QUARTOS')
        );

        $router->post(
            '/painel-dlx/apart-hotel/quartos/excluir',
            [ListaQuartosController::class, 'excluirQuarto']
        )->middlewares(
            new VerificarLogon($this->session),
            new Autorizacao('EXCLUIR_QUARTO')
        );

        $router->get(
            '/painel-dlx/apart-hotel/quartos/novo',
            [CadastrarQuartoController::class, 'formNovoQuarto']
        )->middlewares(
            new VerificarLogon($this->session),
            new Autorizacao('CADASTRAR_NOVO_QUARTO')
        );

        $router->post(
            '/painel-dlx/apart-hotel/quartos/salvar-novo',
            [CadastrarQuartoController::class, 'salvarNovoQuarto']
        )->middlewares(
            new VerificarLogon($this->session),
            new Autorizacao('CADASTRAR_NOVO_QUARTO')
        );

        $router->get(
            '/painel-dlx/apart-hotel/quartos/editar',
            [EditarQuartoController::class, 'formEditarQuarto']
        )->middlewares(
            new VerificarLogon($this->session),
            new Autorizacao('EDITAR_QUARTO')
        );

        $router->post(
            '/painel-dlx/apart-hotel/quartos/atualizar-informacoes',
            [EditarQuartoController::class, 'editarInformacoesQuarto']
        )->middlewares(
            new VerificarLogon($this->session),
            new Autorizacao('EDITAR_QUARTO')
        );
    }
}