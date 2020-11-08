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

namespace Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers;


use DLX\Contracts\TransactionInterface;
use DLX\Core\Exceptions\UserException;
use League\Tactician\CommandBus;
use PainelDLX\Application\Services\Exceptions\ErroAoEnviarEmailException;
use PainelDLX\UseCases\Usuarios\GetUsuarioPeloId\GetUsuarioPeloIdCommand;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Presentation\Web\Common\Controllers\PainelDLXController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Common\Events\EventManagerInterface;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Entities\PedidoItem;
use Reservas\Domain\Pedidos\Events\PagamentoPedidoConfirmado;
use Reservas\Domain\Pedidos\Events\PedidoCancelado;
use Reservas\Domain\Pedidos\Exceptions\PedidoInvalidoException;
use Reservas\Domain\Pedidos\Exceptions\PedidoNaoEncontradoException;
use Reservas\Domain\Quartos\Exceptions\QuartoIndisponivelException;
use Reservas\Domain\Reservas\Exceptions\ReservaInvalidaException;
use Reservas\Domain\Reservas\Exceptions\VisualizarCpfException;
use Reservas\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommand;
use Reservas\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommandHandler;
use Reservas\UseCases\Pedidos\CancelarPedido\CancelarPedidoCommand;
use Reservas\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommand;
use Reservas\UseCases\Pedidos\FindPedidoItemPorId\FindPedidoItemPorIdCommand;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommand;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\PaginaMestraInvalidaException;
use Vilex\Exceptions\TemplateInvalidoException;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class DetalhePedidoController
 * @package Reservas\Presentation\Site\ApartHotel\Controllers
 * @covers DetalhePedidoControllerTest
 */
class DetalhePedidoController extends PainelDLXController
{
    /**
     * @var TransactionInterface
     */
    private $transaction;
    /**
     * @var EventManagerInterface
     */
    private $event_manager;

    /**
     * DetalhePedidoController constructor.
     * @param VileX $view
     * @param CommandBus $commandBus
     * @param SessionInterface $session
     * @param TransactionInterface $transaction
     * @param EventManagerInterface $event_manager
     * @throws TemplateInvalidoException
     */
    public function __construct(
        VileX $view,
        CommandBus $commandBus,
        SessionInterface $session,
        TransactionInterface $transaction,
        EventManagerInterface $event_manager
    ) {
        parent::__construct($view, $commandBus, $session);
        $this->view->addArquivoCss('/vendor/painel-dlx/ui-painel-dlx-reservas/css/aparthotel.tema.css', false, VERSAO_UI_PAINEL_DLX_RESERVAS);
        $this->transaction = $transaction;
        $this->event_manager = $event_manager;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     */
    public function detalhePedido(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        /** @var Usuario $usuario_logado */
        $usuario_logado = $this->session->get('usuario-logado');

        try {
            /** @var Pedido $pedido */
            /* @see GetPedidoPorIdCommandHandler */
            $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($get['id']));

            /** @var Usuario $usuario_logado */
            /* @see GetUsuarioPeloIdCommandHandler */
            $usuario_logado = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario_logado->getId()));

            // Atributos
            $this->view->setAtributo('titulo-pagina', "Pedido #{$pedido->getId()}");
            $this->view->setAtributo('pedido', $pedido);
            $this->view->setAtributo('usuario-logado', $usuario_logado);

            // Views
            $this->view->addTemplate('pedidos/det_pedido');

            // JS
            $this->view->addArquivoJS('/vendor/dlepera88-jquery/jquery-form-ajax/jquery.formajax.plugin-min.js');
            $this->view->addArquivoJS('public/js/apart-hotel-min.js');
        } catch (PedidoNaoEncontradoException | UserException $e) {
            $tipo = $e instanceof PedidoNaoEncontradoException ? 'atencao' : 'erro';

            $this->view->addTemplate('common/mensagem_usuario');
            $this->view->setAtributo('mensagem', [
                'tipo' => $tipo,
                'texto' => $e->getMessage()
            ]);
        }

        return $this->view->render();
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     */
    public function formConfirmarPgtoPedido(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Pedido $pedido */
            /* @see GetPedidoPorIdCommandHandler */
            $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($get['id']));

            // Parâmetros
            $this->view->setAtributo('titulo-pagina', "Confirmar pedido #{$pedido->getId()}");
            $this->view->setAtributo('pedido', $pedido);

            // Visões
            $this->view->addTemplate('pedidos/form_confirmar_pedido');
        } catch (PedidoNaoEncontradoException | UserException $e) {
            $tipo = $e instanceof PedidoNaoEncontradoException ? 'atencao' : 'erro';

            $this->view->addTemplate('common/mensagem_usuario');
            $this->view->setAtributo('mensagem', [
                'tipo' => $tipo,
                'texto' => $e->getMessage()
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
            'id' => FILTER_VALIDATE_INT,
            'motivo' => FILTER_SANITIZE_STRING
        ]);

        /** @var Usuario $usuario_logado */
        $usuario_logado = $this->session->get('usuario-logado');

        try {
            /** @var Pedido $pedido */
            /* @see GetPedidoPorIdCommandHandler */
            $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($post['id']));

            /** @var Usuario $usuario_logado */
            /* @see GetUsuarioPeloIdCommandHandler */
            $usuario_logado = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario_logado->getId()));
            
            /* @see GerarReservasPedidoCommandHandler */
            $this->command_bus->handle(new GerarReservasPedidoCommand($pedido));

            $this->transaction->transactional(function () use ($pedido, $post, $usuario_logado) {
                /* @see ConfirmarPgtoPedidoCommandHandler */
                $this->command_bus->handle(new ConfirmarPgtoPedidoCommand($pedido, $post['motivo'], $usuario_logado));
                $this->event_manager->dispatch(new PagamentoPedidoConfirmado($pedido));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = "Pedido #{$pedido->getId()} foi confirmado com sucesso!";
        } catch (
            ErroAoEnviarEmailException |
            PedidoInvalidoException |
            ReservaInvalidaException |
            QuartoIndisponivelException |
            PedidoNaoEncontradoException
            $e
        ) {
            $retorno = $e instanceof PedidoNaoEncontradoException ? 'atencao' : 'erro';

            $json['retorno'] = $retorno;
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     */
    public function formCancelarPedido(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Pedido $pedido */
            /* @see GetPedidoPorIdCommandHandler */
            $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($get['id']));

            // Parâmetros
            $this->view->setAtributo('titulo-pagina', "Cancelar pedido #{$pedido->getId()}");
            $this->view->setAtributo('pedido', $pedido);

            // Visões
            $this->view->addTemplate('pedidos/form_cancelar_pedido');
        } catch (PedidoNaoEncontradoException | UserException $e) {
            $tipo = $e instanceof PedidoNaoEncontradoException ? 'atencao' : 'erro';

            $this->view->addTemplate('common/mensagem_usuario');
            $this->view->setAtributo('mensagem', [
                'tipo' => $tipo,
                'texto' => $e->getMessage()
            ]);
        }

        return $this->view->render();
    }

    /**
     * Cancelar um pedido
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function cancelarPedido(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'id' => FILTER_VALIDATE_INT,
            'motivo' => FILTER_SANITIZE_STRING
        ]);

        /** @var Usuario $usuario_logado */
        $usuario_logado = $this->session->get('usuario-logado');

        try {
            /** @var Pedido $pedido */
            /* @see GetPedidoPorIdCommandHandler */
            $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($post['id']));

            /** @var Usuario $usuario_logado */
            /* @see GetUsuarioPeloIdCommandHandler */
            $usuario_logado = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario_logado->getId()));

            $this->transaction->transactional(function () use ($pedido, $post, $usuario_logado) {
                /* @see CancelarPedidoCommandHandler */
                $this->command_bus->handle(new CancelarPedidoCommand($pedido, $post['motivo'], $usuario_logado));
                $this->event_manager->dispatch(new PedidoCancelado($pedido));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = "Pedido #{$pedido->getId()} foi cancelado com sucesso!";
        } catch (
            ErroAoEnviarEmailException |
            PedidoInvalidoException |
            ReservaInvalidaException |
            PedidoNaoEncontradoException
            $e
        ) {
            $retorno = $e instanceof PedidoNaoEncontradoException ? 'atencao' : 'erro';

            $json['retorno'] = $retorno;
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

            if (is_null($pedido)) {
                throw new UserException("Pedido #{$get['id']} não encontrado!");
            }

            /** @var Usuario $usuario_logado */
            /* @see GetUsuarioPeloIdCommandHandler */
            $usuario_logado = $this->command_bus->handle(new GetUsuarioPeloIdCommand($usuario->getId()));

            /** @var string $cpf */
            /* @see MostrarCpfCompletoPedidoCommandHandler */
            $cpf = $this->command_bus->handle(new MostrarCpfCompletoPedidoCommand($pedido, $usuario_logado));

            $json['retorno'] = 'sucesso';
            $json['cpf'] = $cpf;
        } catch (VisualizarCpfException | UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     */
    public function detalhamentoPeriodo(ServerRequestInterface $request): ResponseInterface
    {
        $get = $request->getQueryParams();

        try {
            /** @var PedidoItem $pedido_item */
            $pedido_item = $this->command_bus->handle(new FindPedidoItemPorIdCommand($get['pedido_item_id']));

            // Atributos
            $this->view->setAtributo('titulo-pagina', 'Detalhamento do Período');
            $this->view->setAtributo('pedido-item', $pedido_item);

            // View
            $this->view->addTemplate('pedidos/detalhe_periodo');
        } catch (UserException $e) {
            $this->view->addTemplate('common/mensagem_usuario');
            $this->view->setAtributo('mensagem', [
                'tipo' => 'erro',
                'texto' => $e->getMessage()
            ]);
        }

        return $this->view->render();
    }
}