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
use PainelDLX\Presentation\Site\Common\Controllers\PainelDLXController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Exceptions\QuartoNaoEncontradoException;
use Reservas\Domain\Quartos\Exceptions\ValidarQuartoException;
use Reservas\UseCases\Quartos\EditarQuarto\EditarQuartoCommand;
use Reservas\UseCases\Quartos\EditarQuarto\EditarQuartoCommandHandler;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class EditarQuartoController
 * @package Reservas\Presentation\Site\ApartHotel\Controllers
 * @covers EditarQuartoControllerTest
 */
class EditarQuartoController extends PainelDLXController
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
     * Formulário para editar as informações do quarto.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @throws ContextoInvalidoException
     */
    public function formEditarQuarto(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Quarto $quarto */
            /* @see GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($get['id']));

            $this->session->set('editando:quarto', $quarto);

            // Views
            $this->view->addTemplate('quartos/form_quarto');

            // Parâmetros
            $this->view->setAtributo('titulo-pagina', 'Editar quarto');
            $this->view->setAtributo('form-action', '/painel-dlx/apart-hotel/quartos/atualizar-informacoes');
            $this->view->setAtributo('quarto', $quarto);

            // JS
            $this->view->addArquivoJS('/vendor/dlepera88-jquery/jquery-form-ajax/jquery.formajax.plugin-min.js');
            $this->view->addArquivoJS('/vendor/ckeditor/ckeditor/ckeditor.js');
            $this->view->addArquivoJS('public/js/apart-hotel-min.js');
        } catch (QuartoNaoEncontradoException | UserException $e) {
            $tipo = $e instanceof QuartoNaoEncontradoException ? 'atencao' : 'erro';

            $this->view->addTemplate('common/mensagem_usuario');
            $this->view->setAtributo('mensagem', [
                'tipo' => $tipo,
                'texto' => $e->getMessage()
            ]);
        }

        return $this->view->render();
    }

    /**
     * Editar informações do quarto.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function editarInformacoesQuarto(ServerRequestInterface $request): ResponseInterface
    {
        $post = $request->getParsedBody();

        try {
            /** @var Quarto $quarto */
            $quarto = $this->session->get('editando:quarto');

            /* @see EditarQuartoCommandHandler */
            $quarto = $this->command_bus->handle(new EditarQuartoCommand(
                $post['nome'],
                $post['descricao'],
                $post['max_hospedes'],
                $post['qtde'],
                $post['tamanho_m2'],
                $post['valor_min'],
                $post['link'],
                $quarto->getId()
            ));

            $this->session->set('editando:quarto', $quarto);

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = 'Quarto atualizado com sucesso!';
            $json['quarto_id'] = $quarto->getId();
        } catch (ValidarQuartoException | UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }
}