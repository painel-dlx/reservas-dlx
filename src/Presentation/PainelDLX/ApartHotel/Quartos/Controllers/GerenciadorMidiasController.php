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
use Reservas\Domain\Quartos\Exceptions\ValidarQuartoException;
use Reservas\UseCases\Quartos\AdicionarMidiasQuarto\AdicionarMidiasQuartoCommand;
use Reservas\UseCases\Quartos\AdicionarMidiasQuarto\AdicionarMidiasQuartoCommandHandler;
use Reservas\UseCases\Quartos\AdicionarMidiasQuarto\Exceptions\AdicionarMidiasQuartoException;
use Reservas\UseCases\Quartos\EditarQuarto\EditarQuartoCommand;
use Reservas\UseCases\Quartos\EditarQuarto\EditarQuartoCommandHandler;
use Reservas\UseCases\Quartos\ExcluirMidiaQuarto\Exceptions\ExcluirMidiaQuartoException;
use Reservas\UseCases\Quartos\ExcluirMidiaQuarto\ExcluirMidiaQuartoCommand;
use Reservas\UseCases\Quartos\ExcluirMidiaQuarto\ExcluirMidiaQuartoCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Exception\UploadedFileErrorException;
use Zend\Diactoros\Response\JsonResponse;

class GerenciadorMidiasController extends PainelDLXController
{
    /**
     * @var TransactionInterface
     */
    private $transaction;

    /**
     * UploadMidiasController constructor.
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
        $this->transaction = $transaction;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @throws ContextoInvalidoException
     */
    public function formUpload(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Quarto|null $quarto */
        $quarto = $this->session->get('editando:quarto');

        try {
            // Visão
            $this->view->addTemplate('quartos/form_upload');

            // Parâmetro
            $this->view->setAtributo('titulo-pagina', 'Upload de mídias');
            $this->view->setAtributo('quarto', $quarto);
        } catch (UserException $e) {
            $this->view->addTemplate('common/mensagem_usuario');
            $this->view->setAtributo('mensagem', [
                'tipo' => 'erro',
                'texto' => $e->getMessage()
            ]);
        }

        return $this->view->render();
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function uploadMidias(ServerRequestInterface $request): ResponseInterface
    {
        $arquivos = $request->getUploadedFiles();

        /** @var Quarto $quarto */
        $quarto = $this->session->get('editando:quarto');

        try {
            $this->transaction->transactional(function () use ($quarto, $arquivos) {
                $is_auto_salvar = !is_null($quarto->getId());

                /* @see AdicionarMidiasQuartoCommandHandler */
                $this->command_bus->handle(new AdicionarMidiasQuartoCommand($quarto, $arquivos, $is_auto_salvar));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = 'Mídias salvas com sucesso!';
        } catch (AdicionarMidiasQuartoException | ValidarQuartoException | UploadedFileErrorException | UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function excluirMidia(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Quarto $quarto */
        $quarto = $this->session->get('editando:quarto');

        $post = filter_var_array($request->getParsedBody(), [
            'arquivo' => FILTER_SANITIZE_STRING
        ]);

        try {
            $this->transaction->transactional(function () use ($quarto, $post) {
                /* @see ExcluirMidiaQuartoCommandHandler */
                $this->command_bus->handle(new ExcluirMidiaQuartoCommand($quarto, $post['arquivo']));

                if (!is_null($quarto->getId())) {
                    /* @see EditarQuartoCommandHandler */
                    $this->command_bus->handle(new EditarQuartoCommand(
                        $quarto->getNome(),
                        $quarto->getDescricao(),
                        $quarto->getMaximoHospedes(),
                        $quarto->getQuantidade(),
                        $quarto->getTamanhoM2(),
                        $quarto->getValorMinimo(),
                        $quarto->getLink(),
                        $quarto->getId()
                    ));
                }
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = 'Arquivo de mídia excluída com sucesso!';
        } catch (ExcluirMidiaQuartoException | ValidarQuartoException | UploadedFileErrorException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }
}