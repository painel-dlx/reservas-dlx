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

use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Entities\PedidoItem;
use PHPUnit\Framework\TestCase;
use Reservas\Domain\Quartos\Entities\Quarto;

/**
 * Class PedidoItemTest
 * @package Reservas\Tests\Domain\Pedidos\Entities
 * @coversDefaultClass \Reservas\Domain\Pedidos\Entities\PedidoItem
 */
class PedidoItemTest extends TestCase
{
    /**
     * @return PedidoItem
     * @throws Exception
     */
    public function test__construct(): PedidoItem
    {
        $checkin = (new DateTime())->modify('+1 day');
        $checkout = (clone $checkin)->modify('+2 day');

        $quarto = new Quarto('Teste', 10, 10);

        $periodo_estadia = new DatePeriod(
            $checkin,
            new DateInterval('P1D'),
            $checkout
        );

        /** @var DateTime $data */
        foreach ($periodo_estadia as $data) {
            $quarto->addDisponibilidade($data, 10, [1 => 99, 2 => 99], .1);
        }

        $pedido = $this->createMock(Pedido::class);

        /** @var Pedido $pedido */

        $pedido_item = new PedidoItem($pedido, $quarto, $checkin, $checkout);

        // Valores passados pelo construct
        $this->assertInstanceOf(PedidoItem::class, $pedido_item);
        $this->assertSame($pedido, $pedido_item->getPedido());
        $this->assertSame($quarto, $pedido_item->getQuarto());

        // As datas são clonadas para poder configurar os horários corretamente
//        $this->assertSame($checkin, $pedido_item->getCheckin());
//        $this->assertSame($checkout, $pedido_item->getCheckout());

        // Valores setados como padrão
        $this->assertEquals(1, $pedido_item->getQuantidade());
        $this->assertEquals(2, $pedido_item->getQuantidadeAdultos());
        $this->assertEquals(0, $pedido_item->getQuantidadeCriancas());

        // O horário do checkin é 14h e do checkout é 12h
        $this->assertEquals('14:00', $pedido_item->getCheckin()->format('H:i'));
        $this->assertEquals('12:00', $pedido_item->getCheckout()->format('H:i'));

        return $pedido_item;
    }
}
