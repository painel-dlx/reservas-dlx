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

namespace Reservas\PainelDLX\Tests\Domain\Validators\Pedidos;

use Reservas\PainelDLX\Domain\Entities\Pedido;
use Reservas\PainelDLX\Domain\Exceptions\StatusPedidoInvalidoException;
use Reservas\PainelDLX\Domain\Validators\Pedidos\ValidarConfirmarPedido;
use PHPUnit\Framework\TestCase;

class ValidarConfirmarPedidoTest extends TestCase
{

    /**
     * @throws StatusPedidoInvalidoException
     */
    public function test_Validar_deve_lancar_excecao_ao_informar_Pedido_ja_cancelado()
    {
        $pedido = new Pedido();
        $pedido->setId(1);
        $pedido->cancelado();

        $this->expectException(StatusPedidoInvalidoException::class);

        (new ValidarConfirmarPedido())->validar($pedido);
    }

    /**
     * @throws StatusPedidoInvalidoException
     */
    public function test_Validar_deve_lancar_excecao_quando_informar_Pedido_ja_confirmado()
    {
        $pedido = new Pedido();
        $pedido->setId(1);
        $pedido->pago();

        $this->expectException(StatusPedidoInvalidoException::class);

        (new ValidarConfirmarPedido())->validar($pedido);
    }

    /**
     * @throws StatusPedidoInvalidoException
     */
    public function test_Validar_deve_retornar_true_quando_informar_Pedido_pendente()
    {
        $pedido = new Pedido();
        $pedido->setId(1);

        $is_valido = (new ValidarConfirmarPedido())->validar($pedido);
        $this->assertTrue($is_valido);
    }
}
