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

namespace Reservas\Presentation\PainelDLX\ApartHotel\Emails\Controllers;


use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommand;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommandHandler;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;

class EmailPedidosController
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
     * EmailPedidosController constructor.
     * @param VileX $view
     * @param CommandBus $command_bus
     */
    public function __construct(
        VileX $view,
        CommandBus $command_bus
    ) {
        $this->view = $view;
        $this->command_bus = $command_bus;

        $this->view->setPaginaMestra('public/views/paginas-mestras/email-master.phtml');
        $this->view->setViewRoot('public/views/');
    }

    /**
     * Enviar notificação para confirmação de pedido
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @throws ContextoInvalidoException
     */
    public function notificacaoConfirmacaoPedido(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        /** @var Pedido|null $pedido */
        /* @see GetPedidoPorIdCommandHandler */
        $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($get['id']));

        // Views
        $this->view->addTemplate('emails/topo');
        $this->view->addTemplate('emails/confirmacao_pedido');
        $this->view->addTemplate('emails/rodape');

        // Parâmetros
        $this->view->setAtributo('titulo-email', "Pedido #{$pedido->getid()} Confirmado");
        $this->view->setAtributo('pedido', $pedido);

        return $this->view->render();
    }

    /**
     * Enviar notificação para cancelamento de pedido
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     */
    public function notificacaoCancelamentoPedido(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        /** @var Pedido|null $pedido */
        /* @see GetPedidoPorIdCommandHandler */
        $pedido = $this->command_bus->handle(new GetPedidoPorIdCommand($get['id']));

        // Views
        $this->view->addTemplate('emails/topo');
        $this->view->addTemplate('emails/cancelamento_pedido');
        $this->view->addTemplate('emails/rodape');

        // Parâmetros
        $this->view->setAtributo('titulo-email', "Pedido #{$pedido->getid()} Cancelado");
        $this->view->setAtributo('pedido', $pedido);

        return $this->view->render();
    }
}