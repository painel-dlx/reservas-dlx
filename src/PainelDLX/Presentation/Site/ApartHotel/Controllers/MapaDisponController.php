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
use Exception;
use League\Tactician\CommandBus;
use PainelDLX\Presentation\Site\Controllers\SiteController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand;
use Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommandHandler;
use Reservas\PainelDLX\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo\ListaDisponibilidadePorPeriodoCommand;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommand;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommandHandler;
use Reservas\PainelDLX\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use SechianeX\Contracts\SessionInterface;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class MapaDisponController
 * @package Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @covers MapaDisponControllerTest
 */
class MapaDisponController extends SiteController
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
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
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
            /** @var \Reservas\PainelDLX\Domain\Quartos\Entities\Quarto|null $quarto */
            /** @covers GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand((int)$get['quarto']));

            /** @var array $lista_dispon */
            /** @covers ListaDisponibilidadePorPeriodoCommandHandler */
            $lista_dispon = $this->command_bus->handle(new ListaDisponibilidadePorPeriodoCommand($data_inicial, $data_final, $quarto));

            /** @var array $lista_quartos */
            /** @covers ListaQuartosCommandHandler */
            $lista_quartos = $this->command_bus->handle(new ListaQuartosCommand([]));

            // Views
            $this->view->addTemplate('mapa_dispon', [
                'titulo-pagina' => 'Disponibilidade',
                'lista-dispon' => $lista_dispon,
                'lista-quartos' => $lista_quartos,
                'filtro' => [
                    'data_inicial' => $data_inicial->format('Y-m-d'),
                    'data_final' => $data_final->format('Y-m-d'),
                    'quarto' => !is_null($quarto) ? $quarto->getId() : null
                ]
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
            /** @var \Reservas\PainelDLX\Domain\Quartos\Entities\Quarto $quarto */
            /** @covers GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($post['quarto']));

            /** @var Disponibilidade $dispon */
            /** @covers GetDisponibilidadePorDataQuartoCommandHandler */
            $dispon = $this->command_bus->handle(new GetDisponibilidadePorDataQuartoCommand($quarto, $dt_dia));
            $dispon->setQtde($post['qtde']);

            foreach ($post['valor'] as $qtde => $valor) {
                $dispon->setValorPorQtdePessoas($qtde, $valor);
            }

            $this->transaction->transactional(function () use ($dispon) {
                /** @covers SalvarDisponibilidadeQuartoCommandHandler */
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