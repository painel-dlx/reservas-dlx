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


use CPF\CPF;
use DateTime;
use DLX\Contracts\TransactionInterface;
use DLX\Core\Exceptions\UserException;
use Exception;
use League\Tactician\CommandBus;
use PainelDLX\Presentation\Site\Controllers\SiteController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorId\GetQuartoPorIdCommand;
use Reservas\PainelDLX\UseCases\Quartos\ListaQuartos\ListaQuartosCommand;
use Reservas\PainelDLX\UseCases\Reservas\SalvarReserva\SalvarReservaCommand;
use Reservas\PainelDLX\UseCases\Reservas\SalvarReserva\SalvarReservaCommandHandler;
use SechianeX\Contracts\SessionInterface;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Response\JsonResponse;

class SalvarReservaController extends SiteController
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
     * SalvarReservaController constructor.
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
        $this->view->setViewRoot('src/PainelDLX/Presentation/Site/public/views/reservas');
        $this->view->addArquivoCss('src/PainelDLX/Presentation/Site/public/temas/painel-dlx/css/aparthotel.tema.css');

        $this->session = $session;
        $this->transaction = $transaction;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContextoInvalidoException
     * @throws ViewNaoEncontradaException
     * @throws PaginaMestraNaoEncontradaException
     */
    public function formReservarQuarto(ServerRequestInterface $request): ResponseInterface
    {
        try {
            /** @var Quarto[] $lista_quartos */
            /** @covers ListaQuartosCommandHandler */
            $lista_quartos = $this->command_bus->handle(new ListaQuartosCommand(
                ['publicar' => true]
            ));

            // Views
            $this->view->addTemplate('form_reserva');

            // Parâmetros
            $this->view->setAtributo('titulo-pagina', 'Reservar um quarto');
            $this->view->setAtributo('lista-quartos', $lista_quartos);

            // JS
            $this->view->addArquivoJS('/vendor/dlepera88-jquery/jquery-form-ajax/jquery.formajax.plugin-min.js');
            $this->view->addArquivoJS('/vendor/dlepera88-jquery/jquery-mascara/jquery.mascara.plugin-min.js');
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
    public function criarReserva(ServerRequestInterface $request): ResponseInterface
    {
        $post = filter_var_array($request->getParsedBody(), [
            'quarto' => FILTER_VALIDATE_INT,
            'checkin' => FILTER_DEFAULT,
            'checkout' => FILTER_DEFAULT,
            'adultos' => FILTER_VALIDATE_INT,
            'criancas' => FILTER_VALIDATE_INT,
            'hospede' => FILTER_SANITIZE_STRING,
            'cpf' => FILTER_DEFAULT,
            'telefone' => FILTER_DEFAULT,
            'email' => FILTER_VALIDATE_EMAIL
        ]);

        try {
            /** @var Quarto|null $quarto */
            /** @covers GetQuartoPorIdCommandHandler */
            $quarto = $this->command_bus->handle(new GetQuartoPorIdCommand($post['quarto']));

            $dt_checkin = new DateTime($post['checkin']);
            $dt_checkout = new DateTime($post['checkout']);

            $reserva = new Reserva($quarto, $dt_checkin, $dt_checkout, $post['adultos']);
            $reserva->setCriancas($post['criancas']);
            $reserva->setHospede($post['hospede']);
            $reserva->setCpf(new CPF($post['cpf']));
            $reserva->setTelefone($post['telefone']);
            $reserva->setEmail($post['email']);
            $reserva->setOrigem('Painel DLX');
            $reserva->setValor(0); // todo: calcular o valor da reserva

            $this->transaction->transactional(function () use ($reserva) {
                /** @covers SalvarReservaCommandHandler */
                $this->command_bus->handle(new SalvarReservaCommand($reserva));
            });

            $json['retorno'] = 'sucesso';
            $json['mensagem'] = "Reserva #{$reserva->getId()} gerada com sucesso!";
            $json['reserva_id'] = $reserva->getId();
        } catch (UserException $e) {
            $json['retorno'] = 'erro';
            $json['mensagem'] = $e->getMessage();
        }

        return new JsonResponse($json);
    }
}