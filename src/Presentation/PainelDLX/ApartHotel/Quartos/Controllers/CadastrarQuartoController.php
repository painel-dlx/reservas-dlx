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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Exceptions\ValidarQuartoException;
use Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommand;
use Reservas\UseCases\Quartos\CriarNovoQuarto\CriarNovoQuartoCommandHandler;
use Reservas\UseCases\Quartos\GerarDisponibilidadesQuarto\GerarDisponibilidadesQuartoCommand;
use Reservas\UseCases\Quartos\GerarDisponibilidadesQuarto\GerarDisponibilidadesQuartoCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\PaginaMestraInvalidaException;
use Vilex\Exceptions\TemplateInvalidoException;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class CadastrarQuartoController
 * @package Reservas\Presentation\Site\ApartHotel\Controllers
 * @covers CadastrarQuartoControllerTest
 */
class CadastrarQuartoController extends PainelDLXController
{
    /**
     * @var TransactionInterface
     */
    private $transaction;

    /**
     * CadastrarQuartoController constructor.
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
        $this->view->adicionarCss('/vendor/painel-dlx/ui-painel-dlx-reservas/css/aparthotel.tema.css', VERSAO_UI_PAINEL_DLX_RESERVAS);
        $this->transaction = $transaction;
    }

    /**
     * @return ResponseInterface
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
     */
    public function formNovoQuarto(): ResponseInterface
    {
        /** @var Quarto $quarto */
        $quarto = $this->session->get('editando:quarto');

        if (is_null($quarto)) {
            $quarto = new Quarto('', 1, 1);
            $this->session->set('editando:quarto', $quarto);
        }

        try {
            // Parâmetros
            $this->view->setAtributo('form-action', '/painel-dlx/apart-hotel/quartos/salvar-novo');
            $this->view->setAtributo('quarto', $quarto);

            // Views
            $this->view->addTemplate('quartos/form_quarto');

            // JS
            $this->view->adicionarJS('/vendor/dlepera88-jquery/jquery-form-ajax/jquery.formajax.plugin-min.js', VERSAO_UI_PAINEL_DLX_RESERVAS);
            $this->view->adicionarJS('/vendor/ckeditor/ckeditor/ckeditor.js', VERSAO_UI_PAINEL_DLX_RESERVAS);
            $this->view->adicionarJS('public/js/apart-hotel-min.js', VERSAO_UI_PAINEL_DLX_RESERVAS);
        } catch (UserException $e) {
            $this->view->addTemplate('common/mensagem_usuario', [
                'tipo' => 'erro',
                'texto' => $e->getMessage()
            ]);
        } finally {
            $this->view->setAtributo('titulo-pagina', 'Cadastrar novo quarto');
        }

        return $this->view->render();
    }

    /**
     * Salvar um novo quarto.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function salvarNovoQuarto(ServerRequestInterface $request): ResponseInterface
    {
        $post = $request->getParsedBody();

        try {
            $this->transaction->transactional(function () use ($post) {
                /* @see CriarNovoQuartoCommandHandler */
                $quarto = $this->command_bus->handle(new CriarNovoQuartoCommand(
                    $post['nome'],
                    $post['descricao'],
                    $post['max_hospedes'],
                    $post['qtde'],
                    $post['tamanho_m2'],
                    $post['valor_min'],
                    $post['link']
                ));

                $this->session->set('editando:quarto', $quarto);

                /* @see GerarDisponibilidadesQuartoCommandHandler */
                $this->command_bus->handle(new GerarDisponibilidadesQuartoCommand($quarto));
            });

            /** @var Quarto $quarto */
            $quarto = $this->session->get('editando:quarto');

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = 'Quarto cadastrado com sucesso!';
            $json['quarto_id'] = $quarto->getId();
        } catch (ValidarQuartoException | UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }
}