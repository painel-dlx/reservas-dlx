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
use DLX\Core\Exceptions\UserException;
use Exception;
use League\Tactician\CommandBus;
use PainelDLX\Presentation\Site\Common\Controllers\PainelDLXController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand;
use Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommandHandler;
use Reservas\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo\ListaDisponibilidadePorPeriodoCommand;
use Reservas\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommand;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommandHandler;
use Reservas\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class MapaDisponController
 * @package Reservas\Presentation\PainelDLX\ApartHotel\Disponibilidade\Controllers
 * @covers MapaDisponControllerTest
 */
class MapaDisponController extends PainelDLXController
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

        $this->view->setPaginaMestra("public/views/paginas-mestras/{$session->get('vilex:pagina-mestra')}.phtml");
        $this->view->setViewRoot('public/views/');

        $this->view->addArquivoCss('public/temas/painel-dlx/css/aparthotel.tema.css');

        $this->view = $view;
        $this->transaction = $transaction;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PaginaMestraNaoEncontradaException
     * @throws ContextoInvalidoException
     * @throws ViewNaoEncontradaException
     * @throws Exception
     */
    public function calendario(ServerRequestInterface $request): ResponseInterface
    {
        $get = filter_var_array($request->getQueryParams(), [
            'data_inicial' => FILTER_DEFAULT,
            'data_final' => FILTER_DEFAULT,
            'quarto' => ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_NULL_ON_FAILURE]
        ]);

        $data_inicial = new DateTime($get['data_inicial']);
        $data_final = new DateTime($get['data_final']);

        if ($data_inicial->diff($data_final)->days < 14) {
            $data_final = (clone $data_inicial)->modify('+13 days');
        }

        try {
            /** @var Quarto|null $quarto */
            /* @see GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand((int)$get['quarto']));

            /** @var array $lista_dispon */
            /* @see ListaDisponibilidadePorPeriodoCommandHandler */
            $lista_dispon = $this->command_bus->handle(new ListaDisponibilidadePorPeriodoCommand($data_inicial, $data_final, $quarto));

            /** @var array $lista_quartos */
            /* @see ListaQuartosCommandHandler */
            $lista_quartos = $this->command_bus->handle(new ListaQuartosCommand([]));

            // Views
            $this->view->addTemplate('disponibilidade/mapa_dispon');

            // Parâmetros
            $this->view->setAtributo('titulo-pagina', 'Disponibilidade');
            $this->view->setAtributo('lista-dispon', $lista_dispon);
            $this->view->setAtributo('lista-quartos', $lista_quartos);
            $this->view->setAtributo('filtro', [
                'data_inicial' => $data_inicial->format('Y-m-d'),
                'data_final' => $data_final->format('Y-m-d'),
                'quarto' => !is_null($quarto) ? $quarto->getId() : null
            ]);

            // JS
            $this->view->addArquivoJS('/vendor/dlepera88-jquery/jquery-form-ajax/jquery.formajax.plugin-min.js');
            $this->view->addArquivoJS('public/js/apart-hotel-min.js');
        } catch (UserException $e) {
            $this->view->addTemplate('common/mensagem_usuario');
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
     * @throws Exception
     */
    public function salvar(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'quarto' => FILTER_VALIDATE_INT,
            'dia' => FILTER_DEFAULT,
            'qtde' => FILTER_VALIDATE_INT,
            'valor' => ['filter' => FILTER_VALIDATE_FLOAT, 'flags' => FILTER_REQUIRE_ARRAY]
        ]);

        $dt_dia = new DateTime($post['dia']);

        try {
            /** @var Quarto $quarto */
            /* @see GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($post['quarto']));

            /** @var Disponibilidade $dispon */
            /* @see GetDisponibilidadePorDataQuartoCommandHandler */
            $dispon = $this->command_bus->handle(new GetDisponibilidadePorDataQuartoCommand($quarto, $dt_dia));
            $dispon->setQtde($post['qtde']);

            foreach ($post['valor'] as $qtde => $valor) {
                $dispon->setValorPorQtdePessoas($qtde, $valor);
            }

            $this->transaction->transactional(function () use ($dispon) {
                /* @see SalvarDisponibilidadeQuartoCommandHandler */
                $this->command_bus->handle(new SalvarDisponibilidadeQuartoCommand($dispon));
            });

            $json['retorno'] = 'sucesso';
            $json['publicado'] = $dispon->isPublicado();
        } catch (UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
            $json['publicado'] = false;
        }

        return new JsonResponse($json);
    }
}