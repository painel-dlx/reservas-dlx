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

namespace Reservas\PainelDLX\Tests\Domain\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Reservas\PainelDLX\Domain\Entities\Pedido;
use Reservas\Tests\ReservasTestCase;

/**
 * Class PedidoTest
 * @package Reservas\PainelDLX\Tests\Domain\Entities
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Entities\Pedido
 */
class PedidoTest extends ReservasTestCase
{
    /**
     * @return Pedido
     */
    public function test__construct(): Pedido
    {
        $pedido = new Pedido();

        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertInstanceOf(ArrayCollection::class, $pedido->getReservas());
        $this->assertTrue($pedido->isPendente());
        $this->assertEquals('digitada', $pedido->getFormaPgto());

        return $pedido;
    }

    /**
     * @param Pedido $pedido
     * @covers ::isPendente
     * @depends test__construct
     */
    public function test_IsPendente(Pedido $pedido)
    {
        $pedido->setStatus(Pedido::STATUS_PAGO);
        $this->assertFalse($pedido->isPendente());

        $pedido->setStatus(Pedido::STATUS_PENDENTE);
        $this->assertTrue($pedido->isPendente());
    }

    /**
     * @param Pedido $pedido
     * @covers ::isPago
     * @depends test__construct
     */
    public function test_IsPago(Pedido $pedido)
    {
        $pedido->setStatus(Pedido::STATUS_PENDENTE);
        $this->assertFalse($pedido->isPago());

        $pedido->setStatus(Pedido::STATUS_PAGO);
        $this->assertTrue($pedido->isPago());
    }

    /**
     * @param Pedido $pedido
     * @covers ::isCancelado
     * @depends test__construct
     */
    public function test_IsCancelado(Pedido $pedido)
    {
        $pedido->setStatus(Pedido::STATUS_PENDENTE);
        $this->assertFalse($pedido->isCancelado());

        $pedido->setStatus(Pedido::STATUS_CANCELADO);
        $this->assertTrue($pedido->isCancelado());
    }
}
