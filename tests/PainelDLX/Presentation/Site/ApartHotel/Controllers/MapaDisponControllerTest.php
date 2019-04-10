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

namespace Reservas\PainelDLX\Tests\PainelDLX\Presentation\Site\ApartHotel\Controllers;

use DLX\Core\Configure;
use DLX\Infra\EntityManagerX;
use DLX\Infra\ORM\Doctrine\Services\DoctrineTransaction;
use Exception;
use PainelDLX\Application\Factories\CommandBusFactory;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\MapaDisponController;
use Reservas\PainelDLX\Tests\ReservasTestCase;
use SechianeX\Factories\SessionFactory;
use Vilex\VileX;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class MapaDisponControllerTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass \Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\MapaDisponController
 */
class MapaDisponControllerTest extends ReservasTestCase
{
    /**
     * @return MapaDisponController
     * @throws \SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException
     * @throws \SechianeX\Exceptions\SessionAdapterNaoEncontradoException
     * @throws \Doctrine\ORM\ORMException
     * @covers ::__construct
     */
    public function test__construct(): MapaDisponController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $command_bus = CommandBusFactory::create($this->container, Configure::get('app', 'mapping'));

        $controller = new MapaDisponController(
            new VileX(),
            $command_bus(),
            $session,
            new DoctrineTransaction(EntityManagerX::getInstance())
        );

        $this->assertInstanceOf(MapaDisponController::class, $controller);

        return $controller;
    }

    /**
     * @throws \Vilex\Exceptions\ContextoInvalidoException
     * @throws \Vilex\Exceptions\PaginaMestraNaoEncontradaException
     * @throws \Vilex\Exceptions\ViewNaoEncontradaException
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
