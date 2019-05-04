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
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Presentation\Site\Controllers\SiteController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Pedidos\Entities\Pedido;
use Reservas\PainelDLX\Domain\Reservas\Entities\Reserva;
use Reservas\PainelDLX\Domain\Reservas\Exceptions\VisualizarCpfException;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompleto\MostrarCpfCompletoCommand;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommand;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommandHandler;
use Reservas\PainelDLX\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommand;
use Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand;
use Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler;
use Reservas\PainelDLX\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommand;
use Reservas\PainelDLX\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommandHandler;
use Reservas\PainelDLX\UseCases\Reservas\GetReservaPorId\GetReservaPorIdCommand;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

class DetalhePedidoController extends SiteController
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
     * DetalhePedidoController constructor.
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
        $this->view->setViewRoot('src/PainelDLX/Presentation/Site/public/views/pedidos');
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
    public function detalhePedido(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        /** @var Usuario $usuario_logado */
        $usuario_logado = $this->session->get('usuario-logado');

        try {
            /** @var \Reservas\PainelDLX\Domain\Pedidos\Entities\Pedido $pedido */
            /** @see GetPedidoPorIdCommandHandler */
            $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($get['id']));

            /** @var Usuario $usuario_logado */
            /** @see GetUsuarioPeloIdCommandHandler */
            $usuario_logado = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario_logado->getUsuarioId()));

            // Atributos
            $this->view->setAtributo('titulo-pagina', "Pedido #{$pedido->getId()}");
            $this->view->setAtributo('pedido', $pedido);
            $this->view->setAtributo('usuario-logado', $usuario_logado);

            // Views
            $this->view->addTemplate('det_pedido');

            // JS
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
     * Confirmar o pagamento de um pedido.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function confirmarPgtoPedido(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        /** @var Usuario $usuario_logado */
        $usuario_logado = $this->session->get('usuario-logado');

        try {
            /** @var \Reservas\PainelDLX\Domain\Pedidos\Entities\Pedido $pedido */
            /** @see GetPedidoPorIdCommandHandler */
            $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($post['id']));

            /** @var Usuario $usuario_logado */
            /** @see GetUsuarioPeloIdCommandHandler */
            $usuario_logado = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario_logado->getUsuarioId()));
            
            /** @see GerarReservasPedidoCommandHandler */
            $this->command_bus->handle(new GerarReservasPedidoCommand($pedido, $usuario_logado));

            $this->transaction->transactional(function () use ($pedido) {
                /** @see ConfirmarPgtoPedidoCommandHandler */
                $this->command_bus->handle(new ConfirmarPgtoPedidoCommand($pedido));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = "Pedido #{$pedido->getId()} foi confirmado com sucesso!";
        } catch (UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }

    /**
     * Mostrar o CPF completo do cliente de um pedido
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
            /** @var Pedido $pedido */
            /* @see GetPedidoPorIdCommandHandler */
            $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($get['id']));

            /** @var Usuario $usuario_logado */
            /* @see GetUsuarioPeloIdCommandHandler */
            $usuario_logado = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario->getUsuarioId()));

            /** @var string $cpf */
            /* @see MostrarCpfCompletoPedidoCommandHandler */
            $cpf = $this->command_bus->handle(new MostrarCpfCompletoPedidoCommand($pedido, $usuario_logado));

            $json['retorno'] = 'sucesso';
            $json['cpf'] = $cpf;
        } catch (VisualizarCpfException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }
}