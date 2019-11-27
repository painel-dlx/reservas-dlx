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
use PainelDLX\UseCases\ListaRegistros\ConverterFiltro2Criteria\ConverterFiltro2CriteriaCommand;
use PainelDLX\Presentation\Site\Common\Controllers\PainelDLXController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\UseCases\Pedidos\ListaPedidos\ListaPedidosCommand;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;

/**
 * Class ListaPedidoController
 * @package Reservas\Presentation\Site\ApartHotel\Controllers
 * @covers ListaPedidosControllerTest
 */
class ListaPedidosController extends PainelDLXController
{
    /**
     * @var TransactionInterface
     */
    private $transaction;

    /**
     * ListaPedidosController constructor.
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
        $this->view->addArquivoCss('public/temas/painel-dlx/css/aparthotel.tema.css', VERSAO_RESERVAS_DLX);
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
    public function listaPedidos(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'campos' => ['filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_REQUIRE_ARRAY],
            'busca' => FILTER_DEFAULT,
            'pg' => FILTER_VALIDATE_INT,
            'qtde' => FILTER_VALIDATE_INT,
            'offset' => FILTER_VALIDATE_INT
        ]);

        $status = preg_replace('~s$~', '', $args['status']);
        $status = $status === 'confirmado' ? 'pago' : $status;

        try {
            /** @var array $criteria */
            /* @see ConverterFiltro2CriteriaCommandHandler */
            $criteria = $this->command_bus->handle(new ConverterFiltro2CriteriaCommand($get['campos'], $get['busca']));
            $criteria['and'] = ['status' => $status];

            /* @see ListaPedidosCommandHandler */
            $lista_pedidos = $this->command_bus->handle(new ListaPedidosCommand(
                $criteria,
                ['e.id' => $status === 'pendente' ? 'asc' : 'desc'],
                $get['qtde'],
                $get['offset']
            ));

            // Atributos
            $this->view->setAtributo('titulo-pagina', 'Pedidos');
            $this->view->setAtributo('lista-pedidos', $lista_pedidos);
            $this->view->setAtributo('filtro', $get);
            $this->view->setAtributo('status-pedidos', $status);

            // Paginação
            $this->view->setAtributo('pagina-atual', $get['pg']);
            $this->view->setAtributo('qtde-registros-pagina', $get['qtde']);
            $this->view->setAtributo('qtde-registros-lista', count($lista_pedidos));

            // Views
            $this->view->addTemplate('pedidos/lista_pedidos');
            $this->view->addTemplate('common/paginacao');

            // JS
            $this->view->addArquivoJS('public/js/apart-hotel-min.js');
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