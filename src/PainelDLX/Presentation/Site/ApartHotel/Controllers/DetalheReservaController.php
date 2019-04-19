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
use League\Tactician\CommandBus;
use PainelDLX\Presentation\Site\Controllers\SiteController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommand;
use SechianeX\Contracts\SessionInterface;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class DetalheReservaController
 * @package Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @covers DetalheReservaControllerTest
 */
class DetalheReservaController extends SiteController
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
    public function formConfirmarReserva(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Reserva|null $reserva */
            /** @covers GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($get['id']));

            // Atributos
            $this->view->setAtributo('titulo-pagina', "Confirmar reserva #{$reserva->getId()}");
            $this->view->setAtributo('reserva', $reserva);

            // Views
            $this->view->addTemplate('form_confirmar_reserva');
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
     */
    public function formCancelarReserva(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Reserva|null $reserva */
            /** @covers GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($get['id']));

            // Atributos
            $this->view->setAtributo('titulo-pagina', "Cancelar reserva #{$reserva->getId()}");
            $this->view->setAtributo('reserva', $reserva);

            // Views
            $this->view->addTemplate('form_cancelar_reserva');
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
     * Confirma uma reserva.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function confimarReserva(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'id' => FILTER_VALIDATE_INT,
            'motivo' => FILTER_SANITIZE_STRING
        ]);

        /**
         * @var int $id
         * @var string $motivo
         */
        extract($post); unset($post);

        try {
            /** @var Reserva|null $reserva */
            /** @covers GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($id));

            $this->transaction->transactional(function () use ($reserva, $motivo) {
                /** @covers ConfirmarReservaCommandHandler */
                $this->command_bus->handle(new ConfirmarReservaCommand($reserva, $motivo));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = "Reserva #{$reserva->getId()} confirmada com sucesso!";
        } catch (UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }

    /**
     * Cancela uma reserva.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function cancelarReserva(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'id' => FILTER_VALIDATE_INT,
            'motivo' => FILTER_SANITIZE_STRING
        ]);

        /**
         * @var int $id
         * @var string $motivo
         */
        extract($post); unset($post);

        try {
            /** @var Reserva|null $reserva */
            /** @covers GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($id));

            $this->transaction->transactional(function () use ($reserva, $motivo) {
                /** @covers CancelarReservaCommandHandler */
                $this->command_bus->handle(new CancelarReservaCommand($reserva, $motivo));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = "Reserva #{$reserva->getId()} cancelada com sucesso!";
        } catch (UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }
}