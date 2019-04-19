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

namespace Reservas\PainelDLX\Tests\Presentation\Site\ApartHotel\Controllers;

use DLX\Core\Configure;
use DLX\Infra\EntityManagerX;
use DLX\Infra\ORM\Doctrine\Services\DoctrineTransaction;
use Exception;
use PainelDLX\Application\Factories\CommandBusFactory;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\DetalheReservaController;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;
use Vilex\VileX;
use Zend\Diactoros\Response\HtmlResponse;

/**
 * Class DetalheReservaControllerTest
 * @package Reservas\PainelDLX\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass \Reservas\PainelDLX\Presentation\Site\ApartHotel\Controllers\DetalheReservaController
 */
class DetalheReservaControllerTest extends ReservasTestCase
{
    /**
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     */
    public function getRandomReserva(): int
    {
        $query = '
            select
                reserva_id
            from 
                dlx_reservas_cadastro
            order by 
                rand()
            limit 1
        ';

        $sql = EntityManagerX::getInstance()->getConnection()->executeQuery($query);
        return $sql->fetchColumn();
    }

    /**
     * @return DetalheReservaController
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     * @throws \Doctrine\ORM\ORMException
     */
    public function test__construct(): DetalheReservaController
    {
        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');

        $command_bus = CommandBusFactory::create($this->container, Configure::get('app', 'mapping'));

        $controller = new DetalheReservaController(
            new VileX(),
            $command_bus(),
            $session,
            new DoctrineTransaction(EntityManagerX::getInstance())
        );

        $this->assertInstanceOf(DetalheReservaController::class, $controller);

        return $controller;
    }

    /**
     * @param DetalheReservaController $controller
     * @throws Exception
     * @covers ::formConfirmarReserva
     * @depends test__construct
     */
    public function test_FormConfirmarReserva_deve_retornar_um_HtmlResponse(DetalheReservaController $controller)
    {
        $id = $this->getRandomReserva();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['id' => $id]);

        /** @var ServerRequestInterface $request */

        $response = $controller->formConfirmarReserva($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DetalheReservaController $controller
     * @throws Exception
     * @covers ::formCancelarReserva
     * @depends test__construct
     */
    public function test_FormCancelarReserva_deve_retornar_um_HtmlResponse(DetalheReservaController $controller)
    {
        $id = $this->getRandomReserva();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn(['id' => $id]);

        /** @var ServerRequestInterface $request */

        $response = $controller->formCancelarReserva($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DetalheReservaController $controller
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @covers ::confimarReserva
     * @depends test__construct
     */
    public function test_ConfimarReserva_deve_retornar_JsonResponse(DetalheReservaController $controller)
    {
        $id = $this->getRandomReserva();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $id,
            'motivo' => ''
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->confimarReserva($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DetalheReservaController $controller
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\ORMException
     * @covers ::cancelarReserva
     * @depends test__construct
     */
    public function test_CancelarReserva_deve_retornar_JsonResponse(DetalheReservaController $controller)
    {
        $id = $this->getRandomReserva();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $id,
            'motivo' => 'pq sim'
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->cancelarReserva($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }
}
