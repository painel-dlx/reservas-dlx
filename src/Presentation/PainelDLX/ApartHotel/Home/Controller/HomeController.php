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

namespace Reservas\Presentation\PainelDLX\ApartHotel\Home\Controller;


use DLX\Core\Configure;
use League\Tactician\CommandBus;
use PainelDLX\Presentation\Site\Home\Controllers\PaginaInicialController;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;

class HomeController extends PaginaInicialController
{
    /**
     * HomeController constructor.
     * @param VileX $view
     * @param CommandBus $commandBus
     * @param SessionInterface $session
     * @throws ViewNaoEncontradaException
     */
    public function __construct(VileX $view, CommandBus $commandBus, SessionInterface $session)
    {
        parent::__construct($view, $commandBus, $session);
        $this->view->addArquivoCss('/vendor/painel-dlx/ui-painel-dlx-reservas/css/aparthotel.tema.css', false, Configure::get('app', 'versao'));
    }
}