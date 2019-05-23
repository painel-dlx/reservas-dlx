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

use DLX\Core\Configure;
use DLX\Infra\EntityManagerX;
use DLX\Infra\ORM\Doctrine\Services\DoctrineTransaction;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use PainelDLX\Application\Factories\CommandBusFactory;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers\DetalhePedidoController;
use Reservas\Tests\Helpers\PedidoTesteHelper;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;
use Vilex\Exceptions\ContextoInvalidoException;
use Vilex\Exceptions\PaginaMestraNaoEncontradaException;
use Vilex\Exceptions\ViewNaoEncontradaException;
use Vilex\VileX;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class DetalhePedidoControllerTest
 * @package Reservas\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass \Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers\DetalhePedidoController
 */
class DetalhePedidoControllerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return DetalhePedidoController
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     * @throws ORMException
     */
    public function test__construct(): DetalhePedidoController
    {
        /** @var Usuario $usuario */
        $usuario = EntityManagerX::getReference(Usuario::class, 2); // todo: puxar um usuário qualquer do banco de dados

        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');
        $session->set('usuario-logado', $usuario);

        $command_bus = CommandBusFactory::create(self::$container, Configure::get('app', 'mapping'));

        $controller = new DetalhePedidoController(
            new VileX(),
            $command_bus(),
            $session,
            new DoctrineTransaction(EntityManagerX::getInstance())
        );

        $this->assertInstanceOf(DetalhePedidoController::class, $controller);

        return $controller;
    }

    /**
     * @param DetalhePedidoController $controller
     * @throws ContextoInvalidoException
     * @throws ORMException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @throws DBALException
     * @covers ::detalhePedido
     * @depends test__construct
     */
    public function test_DetalhePedido_deve_retornar_HtmlResponse(DetalhePedidoController $controller)
    {
        $id = PedidoTesteHelper::getPedidoIdRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'id' => $id
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->detalhePedido($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DetalhePedidoController $controller
     * @throws ContextoInvalidoException
     * @throws DBALException
     * @throws ORMException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @covers ::formConfirmarPgtoPedido
     * @depends test__construct
     */
    public function test_FormConfirmarPgtoPedido_deve_retornar_HtmlResponse(DetalhePedidoController $controller)
    {
        $id = PedidoTesteHelper::getPedidoIdRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'id' => $id
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->formConfirmarPgtoPedido($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DetalhePedidoController $controller
     * @throws DBALException
     * @throws ORMException
     * @covers ::confirmarPgtoPedido
     * @depends test__construct
     */
    public function test_ConfirmarPgtoPedido_deve_retornar_JsonResponse(DetalhePedidoController $controller)
    {
        $id = PedidoTesteHelper::getPedidoIdRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $id,
            'motivo' => 'Teste unitário'
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->confirmarPgtoPedido($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * @param \Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers\DetalhePedidoController $controller
     * @throws ContextoInvalidoException
     * @throws DBALException
     * @throws ORMException
     * @throws PaginaMestraNaoEncontradaException
     * @throws ViewNaoEncontradaException
     * @covers ::formCancelarPedido
     * @depends test__construct
     */
    public function test_FormCancelarPedido_deve_retornar_HtmlResponse(DetalhePedidoController $controller)
    {
        $id = PedidoTesteHelper::getPedidoIdRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'id' => $id
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->formCancelarPedido($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);
    }

    /**
     * @param DetalhePedidoController $controller
     * @throws DBALException
     * @throws ORMException
     * @covers ::cancelarPedido
     * @depends test__construct
     */
    public function test_CancelarPedido_deve_retornar_JsonResponse(DetalhePedidoController $controller)
    {
        $id = PedidoTesteHelper::getPedidoIdRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $id,
            'motivo' => 'Teste unitário'
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->cancelarPedido($request);
        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}
