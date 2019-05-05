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

namespace Reservas\PainelDLX\Presentation\Site\ApartHotel\Disponibilidade\Controllers;


use DateTime;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Quartos\Exceptions\VerificarDisponQuartoException;
use Reservas\PainelDLX\UseCases\Quartos\FindQuartosDisponiveis\FindQuartosDisponiveisCommand;
use Reservas\PainelDLX\UseCases\Quartos\FindQuartosDisponiveis\FindQuartosDisponiveisCommandHandler;
use Vilex\VileX;

class ListaQuartosDisponiveisController
{
    /**
     * @var VileX
     */
    private $view;
    /**
     * @var CommandBus
     */
    private $command_bus;

    /**
     * ListaQuartosDisponiveisController constructor.
     * @param VileX $view
     * @param CommandBus $command_bus
     */
    public function __construct(
        VileX $view,
        CommandBus $command_bus
    ) {
        $this->view = $view;
        $this->command_bus = $command_bus;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Exception
     */
    public function listaQuartosDisponiveis(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'checkin' => FILTER_DEFAULT,
            'checkout' => FILTER_DEFAULT,
            'adultos' => FILTER_VALIDATE_INT,
            'criancas' => FILTER_VALIDATE_INT,
            'qtde' => FILTER_VALIDATE_INT
        ]);

        $dt_checkin = new DateTime($get['checkin']);
        $dt_checkout = new DateTime($get['checkout']);
        $qtde_hospedes = $get['adultos'] + $get['criancas'];
        $qtde_quartos = $get['qtde'];

        try {
            /** @var array $lista_quartos */
            /* @see FindQuartosDisponiveisCommandHandler */
            $lista_quartos = $this->command_bus->handle(new FindQuartosDisponiveisCommand($dt_checkin, $dt_checkout, $qtde_hospedes, $qtde_quartos));

            $this->view->setAtributo('titulo-pagina', 'Quartos Disponíveis');
            $this->view->setAtributo('lista-quartos', $lista_quartos);
        } catch (VerificarDisponQuartoException $e) {

        }

        return $this->view->render();
    }
}