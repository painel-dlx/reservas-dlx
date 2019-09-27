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

namespace Reservas\Tests\UseCases\Pedidos\PagamentoNaoEfetuado;

use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;
use Reservas\UseCases\Pedidos\PagamentoNaoEfetuado\PagamentoNaoEfetuadoCommand;
use Reservas\UseCases\Pedidos\PagamentoNaoEfetuado\PagamentoNaoEfetuadoCommandHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class PagamentoNaoEfetuadoCommandHandlerTest
 * @package Reservas\Tests\UseCases\Pedidos\PagamentoNaoEfetuado
 * @coversDefaultClass \Reservas\UseCases\Pedidos\PagamentoNaoEfetuado\PagamentoNaoEfetuadoCommandHandler
 */
class PagamentoNaoEfetuadoCommandHandlerTest extends TestCase
{
    /**
     * @covers ::handle
     * @throws Exception
     */
    public function test_Handle_deve_adicionar_historico_de_tentativa_de_pagamento()
    {
        $pedido_repository = $this->createMock(PedidoRepositoryInterface::class);
        $pedido_repository->method('update')->with($this->isInstanceOf(Pedido::class));

        $usuario = $this->createMock(Usuario::class);
        $motivo = 'Teste';

        /** @var PedidoRepositoryInterface $pedido_repository */
        /** @var Usuario $usuario */

        $pedido = new Pedido();

        $command = new PagamentoNaoEfetuadoCommand($pedido, $usuario, $motivo);
        (new PagamentoNaoEfetuadoCommandHandler($pedido_repository))->handle($command);

        $this->assertCount(1, $pedido->getHistorico());
    }
}
