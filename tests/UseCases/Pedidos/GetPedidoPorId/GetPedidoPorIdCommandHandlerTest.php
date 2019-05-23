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

namespace Reservas\Tests\UseCases\Pedidos\GetPedidoPorId;

use DLX\Infra\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommand;
use Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommandHandler;
use Reservas\Tests\Helpers\PedidoTesteHelper;
use Reservas\Tests\ReservasTestCase;

/**
 * Class GetPedidoPorIdCommandHandlerTest
 * @package Reservas\Tests\UseCases\Pedidos\GetPedidoPorId
 * @coversDefaultClass \Reservas\UseCases\Pedidos\GetPedidoPorId\GetPedidoPorIdCommandHandler
 */
class GetPedidoPorIdCommandHandlerTest extends ReservasTestCase
{
    /**
     * @return GetPedidoPorIdCommandHandler
     * @throws ORMException
     */
    public function test__construct(): GetPedidoPorIdCommandHandler
    {
        /** @var \Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface $pedido_repository */
        $pedido_repository = EntityManagerX::getRepository(Pedido::class);
        $handler = new GetPedidoPorIdCommandHandler($pedido_repository);

        $this->assertInstanceOf(GetPedidoPorIdCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param GetPedidoPorIdCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_retornar_null_quando_nao_encontrar_registro_bd(GetPedidoPorIdCommandHandler $handler)
    {
        $command = new GetPedidoPorIdCommand(0);
        $pedido = $handler->handle($command);

        $this->assertNull($pedido);
    }

    /**
     * @param GetPedidoPorIdCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     * @throws ORMException
     * @throws DBALException
     */
    public function test_Handle_deve_retornar_Pedido_quando_encontrar_registro_bd(GetPedidoPorIdCommandHandler $handler)
    {
        $id = PedidoTesteHelper::getPedidoIdRandom();

        $command = new GetPedidoPorIdCommand($id);
        $pedido = $handler->handle($command);

        $this->assertInstanceOf(Pedido::class, $pedido);
    }
}
