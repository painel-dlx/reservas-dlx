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

namespace Reservas\Tests\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers;

use DateInterval;
use DatePeriod;
use DateTime;
use DLX\Infrastructure\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\ORMException;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Tests\TestCase\TesteComTransaction;
use Psr\Http\Message\ServerRequestInterface;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Presentation\PainelDLX\ApartHotel\Pedidos\Controllers\DetalhePedidoController;
use Reservas\Tests\Helpers\PedidoTesteHelper;
use Reservas\Tests\ReservasTestCase;
use SechianeX\Exceptions\SessionAdapterInterfaceInvalidaException;
use SechianeX\Exceptions\SessionAdapterNaoEncontradoException;
use SechianeX\Factories\SessionFactory;
use Vilex\Exceptions\PaginaMestraInvalidaException;
use Vilex\Exceptions\TemplateInvalidoException;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

$_SESSION = [];

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
     * @throws DBALException
     * @throws ORMException
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
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
     * @throws DBALException
     * @throws ORMException
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
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
     * @throws DBALException
     * @throws ORMException
     * @throws PaginaMestraInvalidaException
     * @throws TemplateInvalidoException
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

    /**
     * @test
     * @param DetalhePedidoController $controller
     * @throws DBALException
     * @throws ORMException
     * @depends test__construct
     */
    public function deve_confirmar_pagamento_do_pedido(DetalhePedidoController $controller)
    {
        $query = "
            insert into reservas.Pedido (
                nome, 
                cpf, 
                email, 
                telefone, 
                valor_total, 
                forma_pagamento,
                status
            ) values (
                'Diego Lepera',
                '360.105.668-27',
                'dlepera88@gmail.com',
                '(61) 9 8350-3517',
                100.00,
                'e.Rede',
                'Pendente'
            )
        ";

        $conexao = EntityManagerX::getInstance()->getConnection();
        $conexao->executeQuery($query);
        $pedido_id = $conexao->lastInsertId();

        $checkin = (new DateTime)->modify('+1 day');
        $checkout = (new DateTime)->modify('+3 days');

        $query = '
            insert into reservas.PedidoItem (
                pedido_id, 
                quarto_id, 
                checkin, 
                checkout, 
                quantidade, 
                quantidade_adultos, 
                quantidade_criancas, 
                valor_total
            ) values (
                :pedido_id,
                7,
                :checkin,
                :checkout,
                1,
                1,
                0,
                100.00
            )
        ';

        $sql = $conexao->prepare($query);
        $sql->bindValue(':pedido_id', $pedido_id, ParameterType::INTEGER);
        $sql->bindValue(':checkin', $checkin->format('Y-m-d'), ParameterType::STRING);
        $sql->bindValue(':checkout', $checkout->format('Y-m-d'), ParameterType::STRING);
        $sql->execute();

        $query = "
            insert into reservas.PedidoCartao (pedido_id, dono, numero_cartao, validade, codigo_seguranca, valor) values 
                (:pedido_id, 'Diego Lepera', '1234-5678-9012-3456', '10/2029', '123', 100.00)
        ";

        $sql = $conexao->prepare($query);
        $sql->bindValue(':pedido_id', $pedido_id, ParameterType::INTEGER);
        $sql->execute();

        $conexao->executeQuery('delete from reservas.Disponibilidade where quarto_id = 7');

        $periodo = new DatePeriod($checkin, new DateInterval('P1D'), $checkout);

        $values = [];
        foreach ($periodo as $data) {
            $values[] = "('{$data->format('Y-m-d')}', 7, 1, 0)";
        }

        $query = 'insert into reservas.Disponibilidade (data, quarto_id, quantidade, desconto) values ';
        $query .= implode(', ', $values);
        $conexao->executeQuery($query);

        $conexao->executeQuery('
            insert into reservas.DisponibilidadeValor 
            select
                disponibilidade_id,
                1,
                100.00
            from
                reservas.Disponibilidade
            where
                quarto_id = 7
        ');

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn([
            'id' => $pedido_id,
            'motivo' => 'Teste unitário'
        ]);

        /** @var ServerRequestInterface $request */

        $response = $controller->confirmarPgtoPedido($request);
        $this->assertInstanceOf(JsonResponse::class, $response);

        $json = json_decode((string)$response->getBody());

        $this->assertObjectHasAttribute('retorno', $json);
        $this->assertObjectHasAttribute('mensagem', $json);
        $this->assertEquals('sucesso', $json->retorno);
    }
}
