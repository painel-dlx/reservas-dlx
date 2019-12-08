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
use DLX\Core\Configure;
use League\Tactician\CommandBus;
use PainelDLX\Presentation\Site\Common\Controllers\PainelDLXController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\UseCases\Pedidos\QuantidadePedidosPorStatus\QuantidadePedidosPorStatusCommand;
use Reservas\UseCases\Pedidos\QuantidadePedidosPorStatus\QuantidadePedidosPorStatusCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Throwable;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;

/**
 * Class WidgetPedidoController
 * @package Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers
 * @covers WidgetPedidoControllerTest
 */
class WidgetPedidoController extends PainelDLXController
{
    /**
     * @var TransactionInterface
     */
    private $transaction;

    /**
     * WidgetPedidoController constructor.
     * @param VileX $view
     * @param CommandBus $commandBus
     * @param SessionInterface $session
     * @param TransactionInterface $transaction
     * @throws ViewNaoEncontradaException
     */
    public function __construct(
        VileX $view,
        CommandBus $commandBus,
        SessionInterface $session,
        TransactionInterface $transaction
    ) {
        parent::__construct($view, $commandBus, $session);
        $this->view->addArquivoCss('/vendor/painel-dlx/ui-painel-dlx-reservas/css/aparthotel.tema.css', false, VERSAO_UI_PAINEL_DLX_RESERVAS);
        $this->transaction = $transaction;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return ResponseInterface
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     */
    public function quantidadePedidosPorStatus(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $get = array_merge($request->getQueryParams(), $args);

        try {
            /** @var int $quantidade_pedidos */
            /* @see QuantidadePedidosPorStatusCommandHandler */
            $quantidade_pedidos = $this->command_bus->handle(new QuantidadePedidosPorStatusCommand($get['status'], $get['data_inicial'], $get['data_final']));

            // View
            $this->view->setAtributo('status', $get['status']);
            $this->view->setAtributo('quantidade-pedidos', $quantidade_pedidos);

            $this->view->addTemplate('pedidos/widgets/quantidade_pedidos');
        } catch (Throwable $e) {
            $this->view->addTemplate('common/mensagem_usuario');
            $this->view->setAtributo('mensagem', [
                'tipo' => 'erro',
                'texto' => $e->getMessage()
            ]);
        }

        return $this->view->render();
    }
}