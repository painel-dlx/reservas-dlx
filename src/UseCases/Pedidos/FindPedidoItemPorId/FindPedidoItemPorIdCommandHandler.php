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

namespace Reservas\UseCases\Pedidos\FindPedidoItemPorId;


use Reservas\Domain\Pedidos\Entities\PedidoItem;
use Reservas\Domain\Pedidos\Repositories\PedidoItemRepositoryInterface;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;

class FindPedidoItemPorIdCommandHandler
{
    /**
     * @var PedidoRepositoryInterface
     */
    private $pedido_item_repository;

    /**
     * FindPedidoItemPorIdCommandHandler constructor.
     * @param PedidoItemRepositoryInterface $pedido_repository
     */
    public function __construct(PedidoItemRepositoryInterface $pedido_repository)
    {
        $this->pedido_item_repository = $pedido_repository;
    }

    /**
     * @param FindPedidoItemPorIdCommand $command
     * @return PedidoItem
     */
    public function handle(FindPedidoItemPorIdCommand $command): PedidoItem
    {
        $pedido_item_id = $command->getId();

        /** @var PedidoItem $pedido_item */
        $pedido_item = $this->pedido_item_repository->find($pedido_item_id);

        // @todo: implementar verificação para saber se o registro PedidoItem foi encontrado

        return $pedido_item;
    }
}