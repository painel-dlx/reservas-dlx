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
use Exception;
use Reservas\Domain\Pedidos\Entities\PedidoItem;
use Reservas\Domain\Pedidos\Entities\PedidoItemDetalhe;
use PHPUnit\Framework\TestCase;

/**
 * Class PedidoItemDetalheTest
 * @package Reservas\Tests\Domain\Pedidos\Entities
 * @coversDefaultClass \Reservas\Domain\Pedidos\Entities\PedidoItemDetalhe
 */
class PedidoItemDetalheTest extends TestCase
{
    /**
     * @return PedidoItemDetalhe
     * @throws Exception
     */
    public function test__construct(): PedidoItemDetalhe
    {
        $item = $this->createMock(PedidoItem::class);
        $data = new DateTime;
        $diaria = 123.40;
        $desconto = 0.1;

        /** @var PedidoItem $item */

        $detalhe = new PedidoItemDetalhe($item, $data, $diaria, $desconto);

        $this->assertEquals($item, $detalhe->getItem());
        $this->assertEquals($data, $detalhe->getData());
        $this->assertEquals($diaria, $detalhe->getDiaria());
        $this->assertEquals($desconto, $detalhe->getDesconto());
        $this->assertRegExp('~^\d+%~', $detalhe->getPercentDesconto());

        return $detalhe;
    }
}
