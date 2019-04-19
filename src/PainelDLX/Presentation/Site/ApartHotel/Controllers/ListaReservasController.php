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

namespace Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers;


use DLX\Contracts\TransactionInterface;
use DLX\Core\Exceptions\UserException;
use Doctrine\Common\Collections\Criteria;
use League\Tactician\CommandBus;
use PainelDLX\Application\UseCases\ListaRegistros\ConverterFiltro2Criteria\ConverterFiltro2CriteriaCommand;
use PainelDLX\Presentation\Site\Controllers\SiteController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommand;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\ListaReservas\ListaReservasCommand;
use SechianeX\Contracts\SessionInterface;
use Vilex\VileX;

/**
 * Class ListaReservasController
 * @package Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @covers ListaReservasControllerTest
 */
class ListaReservasController extends SiteController
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var TransactionInterface
     */
    private $transaction;

    /**
     * ListaQuartosController constructor.
     * @param VileX $view
     * @param CommandBus $commandBus
     * @param SessionInterface $session
     * @param TransactionInterface $transaction
     */
    public function __construct(
        VileX $view,
        CommandBus $commandBus,
        SessionInterface $session,
        TransactionInterface $transaction
    ) {
        parent::__construct($view, $commandBus);

        $this->view->setPaginaMestra("src/Presentation/Site/public/views/paginas-mestras/{$session->get('vilex:pagina-mestra')}.phtml");
        $this->view->setViewRoot('src/PainelDLX/Presentation/Site/public/views/reservas');
        $this->view->addArquivoCss('src/PainelDLX/Presentation/Site/public/temas/painel-dlx/css/aparthotel.tema.css');

        $this->session = $session;
        $this->transaction = $transaction;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
     */
    public function listaReservas(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'campos' => ['filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_REQUIRE_ARRAY],
            'busca' => FILTER_DEFAULT
        ]);

        try {
            /**
             * @var array $criteria
             * @covers ConverterFiltro2CriteriaCommandHandler
             */
            $criteria = $this->command_bus->handle(new ConverterFiltro2CriteriaCommand($get['campos'], $get['busca']));

            /** @covers ListaReservasCommandHandler */
            $lista_reservas = $this->command_bus->handle(new ListaReservasCommand(
                $criteria,
                ['e.status' => Criteria::DESC, 'e.checkin' => Criteria::ASC] // todo: verificar pq tenho que informar o alias
            ));

            // Atributos
            $this->view->setAtributo('titulo-pagina', 'Reservas');
            $this->view->setAtributo('lista-reservas', $lista_reservas);
            $this->view->setAtributo('filtro', $get);

            // Views
            $this->view->addTemplate('lista_reservas');

            // JS
            // $this->view->addArquivoJS('src/PainelDLX/Presentation/Site/public/js/apart-hotel.js');
        } catch (UserException $e) {
            $this->view->addTemplate('../mensagem_usuario');
            $this->view->setAtributo('mensagem', [
                'tipo' => 'erro',
                'mensagem' => $e->getMessage()
            ]);
        }

        return $this->view->render();
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
     * todo: transferir esse método para DetalheReservaController
     */
    public function detalhesReserva(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Reserva|null $reserva */
            /** @covers GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($get['id']));

            // Atributos
            $this->view->setAtributo('titulo-pagina', "Reserva #{$reserva->getId()}");
            $this->view->setAtributo('reserva', $reserva);

            // Views
            $this->view->addTemplate('det_reserva');
        } catch (UserException $e) {
            $this->view->addTemplate('../mensagem_usuario');
            $this->view->setAtributo('mensagem', [
                'tipo' => 'erro',
                'mensagem' => $e->getMessage()
            ]);
        }

        return $this->view->render();
    }
}