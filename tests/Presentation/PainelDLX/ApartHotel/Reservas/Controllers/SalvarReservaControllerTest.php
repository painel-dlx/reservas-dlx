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

namespace Reservas\Tests\Presentation\Site\ApartHotel\Controllers;

use DateTime;
use Exception;
use PainelDLX\Tests\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Presentation\PainelDLX\ApartHotel\Reservas\Controllers\SalvarReservaController;
use Reservas\Tests\Helpers\QuartoTesteHelper;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class SalvarReservaControllerTest
 * @package Reservas\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass SalvarReservaController
 */
class SalvarReservaControllerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return SalvarReservaController
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     */
    public function test__construct(): SalvarReservaController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $controller = self::$painel_dlx->getContainer()->get(SalvarReservaController::class);

        $this->assertInstanceOf(SalvarReservaController::class, $controller);

        return $controller;
    }

    /**
     * @param SalvarReservaController $controller
     * @throws ContextoInvalidoException
     * @throws ViewNaoEncontradaException
     * @throws PaginaMestraNaoEncontradaException
     * @covers ::formReservarQuarto
     * @depends test__construct
     */
    public function test_FormReservarQuarto_deve_retornar_HtmlResponse(SalvarReservaController $controller)
    {
        $request = $this->createMock(ServerRequestInterface::class);

        /** @var ServerRequestInterface $request */

        $response = $controller->formReservarQuarto($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param SalvarReservaController $controller
     * @throws Exception
     * @covers ::criarReserva
     * @depends test__construct
     */
    public function test_CriarReserva_deve_retornar_JsonResponse(SalvarReservaController $controller)
    {
        $quarto = QuartoTesteHelper::getRandom();
        $checkin = (new DateTime())->modify('+1 day');
        $checkout = (clone $checkin)->modify('+2 days');

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'quarto' => $quarto->getId(),
            'checkin' => $checkin->format('Y-m-d'),
            'checkout' => $checkout->format('Y-m-d'),
            'adultos' => 1,
            'criancas' => 0,
            'hospede' => 'Nome do Cliente',
            'cpf' => '000.000.000-00',
            'telefone' => '(00) 9 0000-0000',
            'email' => 'cliente@gmail.com'
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->criarReserva($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
