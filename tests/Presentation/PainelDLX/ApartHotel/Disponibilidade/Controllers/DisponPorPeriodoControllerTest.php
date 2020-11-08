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

use DLX\Infrastructure\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Presentation\PainelDLX\ApartHotel\Disponibilidade\Controllers\DisponPorPeriodoController;
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
 * Class DisponPorPeriodoControllerTest
 * @package Reservas\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass \Reservas\Presentation\PainelDLX\ApartHotel\Disponibilidade\Controllers\DisponPorPeriodoController
 */
class DisponPorPeriodoControllerTest extends ReservasTestCase
{
    /**
     * @return DisponPorPeriodoController
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     */
    public function test__construct(): DisponPorPeriodoController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        /** @var DisponPorPeriodoController $controller */
        $controller = self::$painel_dlx->getContainer()->get(DisponPorPeriodoController::class);

        $this->assertInstanceOf(DisponPorPeriodoController::class, $controller);

        return $controller;
    }

    /**
     * @param DisponPorPeriodoController $controller
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @covers ::formDisponPorPeriodo
     * @depends test__construct
     */
    public function test_FormDisponPorPeriodo_deve_retornar_um_HtmlResponse(DisponPorPeriodoController $controller)
    {
        $request = $this->createMock(ServerRequestInterface::class);

        /** @var ServerRequestInterface $request */

        $response = $controller->formDisponPorPeriodo($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DisponPorPeriodoController $controller
     * @throws ContextoInvalidoException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @throws DBALException
     * @throws ORMException
     * @covers ::disponConfigQuarto
     * @depends test__construct
     */
    public function test_DisponConfigQuarto_deve_retornar_um_HtmlResponse(DisponPorPeriodoController $controller)
    {
        $query = '
            select
                quarto_id
            from
                reservas.Quarto
            order by 
                rand()
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->executeQuery($query);
        $id = $sql->fetchColumn();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['id' => $id]);

        /** @var ServerRequestInterface $request */

        $response = $controller->disponConfigQuarto($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DisponPorPeriodoController $controller
     * @throws Exception
     * @covers ::salvarDisponPorPeriodo
     * @depends test__construct
     */
    public function test_SalvarDisponPorPeriodo_deve_retornar_um_JsonResponse(DisponPorPeriodoController $controller)
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'quarto_id' => 7,
            'data_inicial' => date('Y-m-d'),
            'data_final' => date('Y-m-d'),
            'qtde' => 1,
            'valores' => [1 => 99.],
            'desconto' => 0
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->salvarDisponPorPeriodo($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
