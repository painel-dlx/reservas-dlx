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

namespace Reservas\Domain\Pedidos\Validators;


use Reservas\Domain\Pedidos\Contracts\PedidoValidatorInterface;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Entities\PedidoItem;
use Reservas\Domain\Pedidos\Exceptions\PedidoInvalidoException;

/**
 * Class ValidarReservasGeradas
 * @package Reservas\Domain\Pedidos\Validators
 * @covers ValidarReservasGeradasTest
 */
class ValidarReservasGeradas implements PedidoValidatorInterface
{
    /**
     * Valida uma determinada regra sobre um pedido
     * @param Pedido $pedido
     * @return bool
     * @throws PedidoInvalidoException
     */
    public function validar(Pedido $pedido): bool
    {
        $qtde_itens = $pedido->getItens()->count();
        $qtde_reservas = 0;

        /** @var PedidoItem $pedido_item */
        foreach ($pedido->getItens() as $pedido_item) {
            $qtde_reservas += (int)$pedido_item->hasReservaGerada();
        }

        if ($qtde_reservas < 1) {
            throw PedidoInvalidoException::nenhumaReservaEncontrada();
        }

        if ($qtde_reservas !== $qtde_itens) {
            throw PedidoInvalidoException::qtdeReservasDiferenteQtdeItens($qtde_reservas, $qtde_itens);
        }

        return true;
    }
}