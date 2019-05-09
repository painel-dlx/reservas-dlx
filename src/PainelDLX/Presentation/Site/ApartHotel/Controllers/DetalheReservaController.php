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
use PainelDLX\Application\UseCases\Usuarios\GetUsuarioPeloId\GetUsuarioPeloIdCommand;
use PainelDLX\Application\UseCases\Usuarios\GetUsuarioPeloId\GetUsuarioPeloIdCommandHandler;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Presentation\Site\Controllers\SiteController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Quartos\Exceptions\QuartoIndisponivelException;
use Reservas\PainelDLX\Domain\Reservas\Entities\Reserva;
use Reservas\PainelDLX\Domain\Reservas\Exceptions\ReservaInvalidaException;
use Reservas\PainelDLX\Domain\Reservas\Exceptions\VisualizarCpfException;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommand;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\CancelarReserva\CancelarReservaCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\ConfirmarReserva\ConfirmarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommand;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class DetalheReservaController
 * @package Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @see DetalheReservaControllerTest
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
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     */
    public function detalhesReserva(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        /** @var Usuario $usuario */
        $usuario = $this->session->get('usuario-logado');

        try {
            /** @var Reserva|null $reserva */
            /** @see GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($get['id']));

            /** @var Usuario $usuario_logado */
            /** @see GetUsuarioPeloIdCommandHandler */
            $usuario_logado = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario->getUsuarioId()));

            // Atributos
            $this->view->setAtributo('titulo-pagina', "Reserva #{$reserva->getId()}");
            $this->view->setAtributo('reserva', $reserva);
            $this->view->setAtributo('usuario-logado', $usuario_logado);

            // Views
            $this->view->addTemplate('det_reserva');

            // JS
            $this->view->addArquivoJS('/vendor/dlepera88-jquery/jquery-form-ajax/jquery.formajax.plugin-min.js');
            $this->view->addArquivoJS('src/PainelDLX/Presentation/Site/public/js/apart-hotel.js');
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
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     */
    public function formConfirmarReserva(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Reserva|null $reserva */
            /** @see GetReservaPorIdCommandHandler */
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
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     */
    public function formCancelarReserva(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Reserva|null $reserva */
            /** @see GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($get['id']));

            // Atributos
            $this->view->setAtributo('titulo-pagina', "Cancelar reserva #{$reserva->getId()}");
            $this->view->setAtributo('reserva', $reserva);

            // Views
            $this->view->addTemplate('form_cancelar_reserva');
        } catch (ReservaInvalidaException $e) {
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
    public function confirmarReserva(ServerRequestInterface $request): ResponseInterface
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
            /** @see GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($id));

            /** @var Usuario|null $usuario_logado */
            $usuario_logado = $this->session->get('usuario-logado');

            /** @var Usuario|null $usuario */
            $usuario = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario_logado->getUsuarioId()));

            $this->transaction->transactional(function () use ($reserva, $usuario, $motivo) {
                /** @see ConfirmarReservaCommandHandler */
                $this->command_bus->handle(new ConfirmarReservaCommand($reserva, $usuario, $motivo));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = "Reserva #{$reserva->getId()} confirmada com sucesso!";
        } catch (QuartoIndisponivelException | ReservaInvalidaException $e) {
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
            /** @see GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($id));

            /** @var Usuario|null $usuario_logado */
            $usuario_logado = $this->session->get('usuario-logado');

            /** @var Usuario|null $usuario */
            $usuario = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario_logado->getUsuarioId()));

            $this->transaction->transactional(function () use ($reserva, $usuario, $motivo) {
                /** @see CancelarReservaCommandHandler */
                $this->command_bus->handle(new CancelarReservaCommand($reserva, $usuario, $motivo));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = "Reserva #{$reserva->getId()} cancelada com sucesso!";
        } catch (ReservaInvalidaException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }

    /**
     * Mostrar o CPF completo do cliente de uma reserva
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function mostrarCpfCompleto(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        /** @var Usuario $usuario */
        $usuario = $this->session->get('usuario-logado');

        try {
            /** @var Reserva $reserva */
            /** @see GetReservaPorIdCommandHandler */
            $reserva = $this->command_bus->handle(new GetReservaPorIdCommand($get['id']));

            /** @var Usuario $usuario_logado */
            /** @see GetUsuarioPeloIdCommandHandler */
            $usuario_logado = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario->getUsuarioId()));

            /** @var string $cpf */
            /** @see MostrarCpfCompletoCommandHandler */
            $cpf = $this->command_bus->handle(new MostrarCpfCompletoCommand($reserva, $usuario_logado));

            $json['retorno'] = 'sucesso';
            $json['cpf'] = $cpf;
        } catch (VisualizarCpfException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }
}