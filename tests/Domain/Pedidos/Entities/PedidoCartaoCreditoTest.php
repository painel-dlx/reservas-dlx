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

namespace Reservas\Tests\Domain\Pedidos\Entities;

use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Entities\PedidoCartaoCredito;
use PHPUnit\Framework\TestCase;

/**
 * Class PedidoCartaoCreditoTest
 * @package Reservas\Tests\Domain\Pedidos\Entities
 * @coversDefaultClass \Reservas\Domain\Pedidos\Entities\PedidoCartaoCredito
 */
class PedidoCartaoCreditoTest extends TestCase
{
    /**
     * @return PedidoCartaoCredito
     */
    public function test__construct(): PedidoCartaoCredito
    {
        $dono = 'Dono do Cartão';
        $numero_cartao = '5400-0000-0000-0000';
        $validade = '12/2030';
        $codigo_seguranca = '123';
        $valor = 1234.;

        $pedido = $this->createMock(Pedido::class);
        $pedido->method('getValorTotal')->willReturn($valor);

        /** @var Pedido $pedido */

        $cartao_credito = new PedidoCartaoCredito(
            $pedido,
            $dono,
            $numero_cartao,
            $validade,
            $codigo_seguranca
        );

        $this->assertInstanceOf(PedidoCartaoCredito::class, $cartao_credito);

        $this->assertSame($pedido, $cartao_credito->getPedido());
        $this->assertSame($numero_cartao, $cartao_credito->getNumeroCartao());
        $this->assertSame($validade, $cartao_credito->getValidade());
        $this->assertSame($codigo_seguranca, $cartao_credito->getCodigoSeguranca());
        $this->assertSame($valor, $cartao_credito->getValor());
        $this->assertEquals(1, $cartao_credito->getParcelas());

        // O construct identifica a bandeira do cartão
        $this->assertEquals('MasterCard', $cartao_credito->getBandeira());

        return $cartao_credito;
    }
}
