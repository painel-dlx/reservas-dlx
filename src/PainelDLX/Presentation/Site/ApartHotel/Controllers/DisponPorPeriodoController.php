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


use DateTime;
use DLX\Contracts\TransactionInterface;
use DLX\Core\Exceptions\UserException;
use League\Tactician\CommandBus;
use PainelDLX\Presentation\Site\Controllers\SiteController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommand;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\PainelDLX\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use SechianeX\Contracts\SessionInterface;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class DisponPorPeriodoController
 * @package Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @covers DisponPorPeriodoControllerTest
 */
class DisponPorPeriodoController extends SiteController
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
     */
    public function __construct(
        VileX $view,
        CommandBus $commandBus,
        SessionInterface $session,
        TransactionInterface $transaction
    ) {
        parent::__construct($view, $commandBus);

        $this->view->setPaginaMestra("src/Presentation/Site/public/views/paginas-mestras/{$session->get('vilex:pagina-mestra')}.phtml");
        $this->view->setViewRoot('src/PainelDLX/Presentation/Site/public/views/disponibilidade');

        $this->view->addArquivoCss('src/PainelDLX/Presentation/Site/public/temas/painel-dlx/css/aparthotel.tema.css');

        $this->view = $view;
        $this->transaction = $transaction;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
     */
    public function formDisponPorPeriodo(ServerRequestInterface $request): ResponseInterface
    {
        try {
            /** @var array $lista_quartos */
            /** @covers ListaQuartosCommandHandler */
            $lista_quartos = $this->command_bus->handle(new ListaQuartosCommand([]));

            // Views
            $this->view->addTemplate('form_dispon_periodo', [
                'titulo-pagina' => 'Disponibilidade por período',
                'lista-quartos' => $lista_quartos
            ]);

            // JS
            $this->view->addArquivoJS('/vendor/dlepera88-jquery/jquery-form-ajax/jquery.formajax.plugin-min.js');
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
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
     */
    public function disponConfigQuarto(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'id' => FILTER_VALIDATE_INT
        ]);

        try {
            /** @var Quarto $quarto */
            /** @covers GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($get['id']));

            // Views
            $this->view->addTemplate('dispon_config_quarto', [
                'quarto' => $quarto
            ]);
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
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
     * @throws \Exception
     */
    public function salvarDisponPorPeriodo(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'quarto_id' => FILTER_VALIDATE_INT,
            'data_inicial' => FILTER_DEFAULT,
            'data_final' => FILTER_DEFAULT,
            'qtde' => FILTER_VALIDATE_INT,
            'valores' => ['filter' => FILTER_VALIDATE_FLOAT, 'flags' => FILTER_REQUIRE_ARRAY]
        ]);

        $data_inicial = new DateTime($post['data_inicial']);
        $data_final = new DateTime($post['data_final']);

        try {
            /** @var Quarto $quarto */
            /** @covers GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($post['quarto_id']));

            $this->transaction->transactional(function () use ($quarto, $data_inicial, $data_final, $post) {
                /** @covers SalvarDisponPeriodoCommandHandler */
                $this->command_bus->handle(new SalvarDisponPeriodoCommand(
                    $data_inicial,
                    $data_final,
                    $quarto,
                    $post['qtde'],
                    $post['valores']
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