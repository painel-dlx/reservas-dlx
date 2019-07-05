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

use DateTime;
use Reservas\Domain\Pedidos\Entities\PedidoHistorico;
use PHPUnit\Framework\TestCase;

/**
 * Class PedidoHistoricoTest
 * @package Reservas\Tests\Domain\Pedidos\Entities
 * @coversDefaultClass \Reservas\Domain\Pedidos\Entities\PedidoHistorico
 */
class PedidoHistoricoTest extends TestCase
{
    /**
     * @return PedidoHistorico
     */
    public function test__construct(): PedidoHistorico
    {
        $status = 'Pago';
        $motivo = 'Teste de Motivo';

        $pedido_historico = new PedidoHistorico($status, $motivo);

        $this->assertInstanceOf(PedidoHistorico::class, $pedido_historico);
        $this->assertEquals($status, $pedido_historico->getStatus());
        $this->assertEquals($motivo, $pedido_historico->getMotivo());

        $this->assertInstanceOf(DateTime::class, $pedido_historico->getData());
        $this->assertEquals(new DateTime(), $pedido_historico->getData(), '', 0.1);

        return $pedido_historico;
    }
}
