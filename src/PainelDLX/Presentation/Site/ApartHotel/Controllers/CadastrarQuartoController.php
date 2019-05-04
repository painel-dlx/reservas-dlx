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
use PainelDLX\Presentation\Site\Controllers\SiteController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Quartos\Exceptions\ValidarQuartoException;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\SalvarQuarto\SalvarQuartoCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class CadastrarQuartoController
 * @package Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @covers CadastrarQuartoControllerTest
 */
class CadastrarQuartoController extends SiteController
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
     * Formulário para cadastrar um novo quarto.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
     */
    public function formNovoQuarto(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // Views
            $this->view->addTemplate('form_quarto', [
                'titulo-pagina' => 'Cadastrar novo quarto',
                'form-action' => '/painel-dlx/apart-hotel/quartos/salvar-novo',
                'quarto' => new Quarto('', 1, 1)
            ]);

            // JS
            $this->view->addArquivoJS('/vendor/dlepera88-jquery/jquery-form-ajax/jquery.formajax.plugin-min.js');
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
     * Salvar um novo quarto.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function salvarNovoQuarto(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'nome' => ['filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_EMPTY_STRING_NULL],
            'descricao' => ['filter' => FILTER_DEFAULT, 'flags' => FILTER_FLAG_EMPTY_STRING_NULL],
            'max_hospedes' => FILTER_VALIDATE_INT,
            'qtde' => FILTER_VALIDATE_INT,
            'tamanho_m2' => FILTER_VALIDATE_INT,
            'valor_min' => FILTER_VALIDATE_FLOAT,
            'link' => ['filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_FLAG_EMPTY_STRING_NULL]
        ]);

        try {
            $quarto = new Quarto($post['nome'], $post['qtde'], $post['valor_min']);
            $quarto
                ->setDescricao($post['descricao'])
                ->setMaxHospedes($post['max_hospedes'])
                ->setTamanhoM2($post['tamanho_m2'])
                ->setLink($post['link']);

            /** @covers SalvarQuartoCommandHandler */
            $this->command_bus->handle(new SalvarQuartoCommand($quarto));

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