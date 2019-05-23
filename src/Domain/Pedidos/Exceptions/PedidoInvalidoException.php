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

namespace Reservas\Domain\Pedidos\Exceptions;


use DLX\Core\Exceptions\UserException;

class PedidoInvalidoException extends UserException
{
    /**
     * @return PedidoInvalidoException
     */
    public static function nenhumaReservaEncontrada(): self
    {
        return new static('Antes de confirmar o pagamento do pedido, é necessário gerar as reservas com base nos itens.', 10);
    }

    /**
     * @param int $qtde_reservas
     * @param int $qtde_itens
     * @return PedidoInvalidoException
     */
    public static function qtdeReservasDiferenteQtdeItens(int $qtde_reservas, int $qtde_itens): self
    {
        return new static("A quantidade de reservas geradas ({$qtde_reservas}) é diferente da quantidade de itens do pedido ({$qtde_itens}).", 11);
    }

    /**
     * @param int $pedido_id
     * @return PedidoInvalidoException
     */
    public static function pedidoJaConfirmado(int $pedido_id)
    {
        return new self("Pedido #{$pedido_id} já está confirmado!", 12);
    }

    /**
     * @param int $pedido_id
     * @return PedidoInvalidoException
     */
    public static function pedidoJaCancelado(int $pedido_id)
    {
        return new self("Pedido #{$pedido_id} já está cancelado!", 13);
    }
}