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
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Tests\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Pedidos\Exceptions\PedidoNaoEncontradoException;
use Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers\DetalhePedidoController;
use Reservas\Tests\Helpers\PedidoTesteHelper;
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
 * Class DetalhePedidoControllerTest
 * @package Reservas\Tests\Presentation\Site\ApartHotel\Controllers
 * @coversDefaultClass DetalhePedidoController
 */
class DetalhePedidoControllerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return DetalhePedidoController
     * @throws SessionAdapterInterfaceInvalidaException
     * @throws SessionAdapterNaoEncontradoException
     */
    public function test__construct(): DetalhePedidoController
    {
        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getId')->willReturn(2);

        /** @var Usuario $usuario */

        $session = SessionFactory::createPHPSession();
        $session->set('vilex:pagina-mestra', 'painel-dlx-master');
        $session->set('usuario-logado', $usuario);

        $controller = self::$painel_dlx->getContainer()->get(DetalhePedidoController::class);

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
        $pedido_id = PedidoTesteHelper::getPedidoIdRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'id' => $pedido_id
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->formConfirmarPgtoPedido($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);

        if (!$pedido_id) {
            $this->assertRegExp('~Pedido não encontrado com o ID informado: \d+.~', (string)$response->getBody());
        }
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
        $pedido_id = PedidoTesteHelper::getPedidoIdRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $pedido_id,
            'motivo' => 'Teste unitário'
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->confirmarPgtoPedido($request);
        $this->assertInstanceOf(JsonResponse::class, $response);

        $json = json_decode((string)$response->getBody());

        if (empty($pedido_id)) {
            $this->assertEquals('atencao', $json->retorno);
            $this->assertRegExp('~Pedido não encontrado com o ID informado: \d+.~', $json->mensagem);
        }
    }

    /**
     * @param DetalhePedidoController $controller
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
        $pedido_id = PedidoTesteHelper::getPedidoIdRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getQueryParams')->willReturn([
            'id' => $pedido_id
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->formCancelarPedido($request);
        $this->assertInstanceOf(HtmlResponse::class, $response);

        if (!$pedido_id) {
            $this->assertRegExp('~Pedido não encontrado com o ID informado: \d+.~', (string)$response->getBody());
        }
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
        $pedido_id = PedidoTesteHelper::getPedidoIdRandom();

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $pedido_id,
            'motivo' => 'Teste unitário'
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->cancelarPedido($request);
        $this->assertInstanceOf(JsonResponse::class, $response);

        $json = json_decode((string)$response->getBody());

        if (empty($pedido_id)) {
            $this->assertEquals('atencao', $json->retorno);
            $this->assertRegExp('~Pedido não encontrado com o ID informado: \d+.~', $json->mensagem);
        }
    }
}
