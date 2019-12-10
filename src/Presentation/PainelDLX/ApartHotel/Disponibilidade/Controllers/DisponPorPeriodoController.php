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

namespace Reservas\Presentation\PainelDLX\ApartHotel\Disponibilidade\Controllers;


use DateTime;
use DLX\Contracts\TransactionInterface;
use DLX\Core\Configure;
use DLX\Core\Exceptions\UserException;
use Exception;
use League\Tactician\CommandBus;
use PainelDLX\Presentation\Site\Common\Controllers\PainelDLXController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommand;
use Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommandHandler;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class DisponPorPeriodoController
 * @package Reservas\Presentation\Site\ApartHotel\Controllers
 * @covers DisponPorPeriodoControllerTest
 */
class DisponPorPeriodoController extends PainelDLXController
{
    /**
     * @var TransactionInterface
     */
    private $transaction;

    /**
     * MapaDisponController constructor.
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
     * @return ResponseInterface
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     */
    public function formDisponPorPeriodo(ServerRequestInterface $request): ResponseInterface
    {
        try {
            /** @var array $lista_quartos */
            /* @see ListaQuartosCommandHandler */
            $lista_quartos = $this->command_bus->handle(new ListaQuartosCommand([]));

            // Views
            $this->view->addTemplate('disponibilidade/form_dispon_periodo');

            // Parâmetros
            $this->view->setAtributo('titulo-pagina', 'Disponibilidade por período');
            $this->view->setAtributo('lista-quartos', $lista_quartos);

            // JS
            $versao = Configure::get('app', 'versao');
            $this->view->addArquivoJS('/vendor/dlepera88-jquery/jquery-form-ajax/jquery.formajax.plugin-min.js', false, VERSAO_RESERVAS_DLX);
            $this->view->addArquivoJS('public/js/apart-hotel-min.js', false, VERSAO_RESERVAS_DLX);
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
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     */
    public function disponConfigQuarto(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Quarto $quarto */
            /* @see GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($get['id']));

            // Views
            $this->view->addTemplate('disponibilidade/dispon_config_quarto');

            // Parâmetros
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
     * @throws Exception
     */
    public function salvarDisponPorPeriodo(ServerRequestInterface $request): ResponseInterface
    {
        $post = $request->getParsedBody();

        $data_inicial = new DateTime($post['data_inicial']);
        $data_final = new DateTime($post['data_final']);

        try {
            /** @var Quarto $quarto */
            /* @see GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($post['quarto_id']));

            $this->transaction->transactional(function () use ($quarto, $data_inicial, $data_final, $post) {
                /* @see SalvarDisponPeriodoCommandHandler */
                $this->command_bus->handle(new SalvarDisponPeriodoCommand(
                    $data_inicial,
                    $data_final,
                    $quarto,
                    $post['qtde'],
                    $post['valores'],
                    $post['desconto']
                ));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = "Disponibilidade no período de {$data_inicial->format('d/m/Y')} até {$data_final->format('d/m/Y')} salva com sucesso!";
        } catch (UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }
}