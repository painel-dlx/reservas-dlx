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

use Exception;
use PainelDLX\Tests\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Presentation\PainelDLX\ApartHotel\Disponibilidade\Controllers\MapaDisponController;
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
 * Class MapaDisponControllerTest
 * @package Reservas\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass MapaDisponController
 */
class MapaDisponControllerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return MapaDisponController
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     * @covers ::__construct
     */
    public function test__construct(): MapaDisponController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $controller = self::$painel_dlx->getContainer()->get(MapaDisponController::class);

        $this->assertInstanceOf(MapaDisponController::class, $controller);

        return $controller;
    }

    /**
     * @param MapaDisponController $controller
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @covers ::calendario
     * @depends test__construct
     */
    public function test_Calendario_deve_retornar_HtmlResponse(MapaDisponController $controller)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'data_inicial' => '2019-01-01',
            'data_final' => '2019-02-28'
        ]);

        /** @var ServerRequestInterface $request */
        $response = $controller->calendario($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param MapaDisponController $controller
     * @throws Exception
     * @covers ::salvar
     * @depends test__construct
     */
    public function test_Salvar_deve_retornar_JsonResponse(MapaDisponController $controller)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'quarto' => 1,
            'dia' => '2019-01-30',
            'qtde' => 3,
            'valor' => [
                1 => 79,
                2 => 139
            ]
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->salvar($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
