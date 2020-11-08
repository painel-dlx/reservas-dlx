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

namespace Reservas\Presentation\PainelDLX\ApartHotel\Quartos\Controllers;


use DLX\Contracts\TransactionInterface;
use DLX\Core\Exceptions\UserException;
use League\Tactician\CommandBus;
use PainelDLX\Presentation\Web\Common\Controllers\PainelDLXController;
use PainelDLX\UseCases\ListaRegistros\ConverterFiltro2Criteria\ConverterFiltro2CriteriaCommand;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommand;
use Reservas\UseCases\Quartos\ExcluirQuarto\ExcluirQuartoCommandHandler;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use Reservas\UseCases\Quartos\ListaQuartos\ListaQuartosCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\PaginaMestraInvalidaException;
use Vilex\Exceptions\TemplateInvalidoException;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

class ListaQuartosController extends PainelDLXController
{
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
     * @throws TemplateInvalidoException
     */
    public function __construct(
        VileX $view,
        CommandBus $commandBus,
        SessionInterface $session,
        TransactionInterface $transaction
    ) {
        parent::__construct($view, $commandBus, $session);
        $this->transaction = $transaction;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     */
    public function listaQuartos(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'campos' => ['filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_REQUIRE_ARRAY],
            'busca' => FILTER_DEFAULT,
            'pg' => FILTER_VALIDATE_INT,
            'qtde' => FILTER_VALIDATE_INT,
            'offset' => FILTER_VALIDATE_INT
        ]);

        try {
            $this->session->unset('editando:quarto');

            /** @var array $criteria */
            /* @see ConverterFiltro2CriteriaCommandHandler */
            $criteria = $this->command_bus->handle(new ConverterFiltro2CriteriaCommand($get['campos'], $get['busca']));

            /* @see ListaQuartosCommandHandler */
            $lista_quartos = $this->command_bus->handle(new ListaQuartosCommand(
                $criteria,
                [],
                $get['qtde'],
                $get['offset']
            ));

            // Atributos
            $this->view->setAtributo('titulo-pagina', 'Quartos');
            $this->view->setAtributo('lista-quartos', $lista_quartos);
            $this->view->setAtributo('filtro', $get);

            // Paginação
            $this->view->setAtributo('pagina-atual', $get['pg']);
            $this->view->setAtributo('qtde-registros-pagina', $get['qtde']);
            $this->view->setAtributo('qtde-registros-lista', count($lista_quartos));

            // Views
            $this->view->addTemplate('quartos/lista_quartos');
            $this->view->addTemplate('common/paginacao');

            // JS
            $this->view->addArquivoJS('public/js/apart-hotel-min.js', VERSAO_UI_PAINEL_DLX_RESERVAS);
        } catch (UserException $e) {
            $this->view->addTemplate('common/mensagem_usuario', [
                'tipo' => 'erro',
                'texto' => $e->getMessage()
            ]);
        }

        return $this->view->render();
    }

    /**
     * Excluir um quarto
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function excluirQuarto(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Quarto $quarto */
            /* @see GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($post['id']));

            if (!$quarto instanceof Quarto) {
                throw new UserException('Quarto não localizado!');
            }

            // todo: Usando transação o doctrine dispara um erro. Verificar outras formas de fazer isso!
            // $this->transaction->begin();

            /* @see ExcluirQuartoCommandHandler */
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