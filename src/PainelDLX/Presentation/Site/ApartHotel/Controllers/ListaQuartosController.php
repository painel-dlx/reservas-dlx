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
use PainelDLX\Application\UseCases\ListaRegistros\ConverterFiltro2Criteria\ConverterFiltro2CriteriaCommand;
use PainelDLX\Presentation\Site\Controllers\SiteController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\PainelDLX\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use Reservas\PainelDLX\UseCases\Quartos\ListaQuartos\ListaQuartosCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

class ListaQuartosController extends SiteController
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
        $this->view->setViewRoot('src/PainelDLX/Presentation/Site/public/views/quartos');

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
    public function listaQuartos(ServerRequestInterface $request): ResponseInterface
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

            /** @covers ListaQuartosCommandHandler */
            $lista_quartos = $this->command_bus->handle(new ListaQuartosCommand($criteria));

            // Atributos
            $this->view->setAtributo('titulo-pagina', 'Quartos');
            $this->view->setAtributo('lista-quartos', $lista_quartos);
            $this->view->setAtributo('filtro', $get);

            // Views
            $this->view->addTemplate('lista_quartos');

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
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function excluirQuarto(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /**
             * @var Quarto $quarto
             * @covers GetQuartoPorIdCommandHandler
             */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($post['id']));

            if (!$quarto instanceof Quarto) {
                throw new UserException('Quarto não localizado!');
            }

            // todo: Usando transação o doctrine dispara um erro. Verificar outras formas de fazer isso!
            // $this->transaction->begin();

            /** @covers ExcluirQuartoCommandHandler */
            $this->command_bus->handle(new ExcluirQuartoCommand($quarto));

            // $this->transaction->commit();

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = 'Quarto excluído com sucesso!';
        } catch (UserException $e) {
            // $this->transaction->rollback();

            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }
}