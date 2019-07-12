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

namespace Reservas\UseCases\Pedidos\SalvarPedido;


use CPF\CPF;
use DateTime;
use Exception;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Exceptions\PedidoInvalidoException;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Tests\UseCases\Pedidos\SalvarPedido\SalvarPedidoCommandHandlerTest;

/**
 * Class SalvarPedidoCommandHandler
 * @package Reservas\UseCases\Pedidos\SalvarPedido
 * @covers SalvarPedidoCommandHandlerTest
 */
class SalvarPedidoCommandHandler
{
    /**
     * @var PedidoRepositoryInterface
     */
    private $pedido_repository;
    /**
     * @var QuartoRepositoryInterface
     */
    private $quarto_repository;

    /**
     * SalvarPedidoCommandHandler constructor.
     * @param PedidoRepositoryInterface $pedido_repository
     * @param QuartoRepositoryInterface $quarto_repository
     */
    public function __construct(
        PedidoRepositoryInterface $pedido_repository,
        QuartoRepositoryInterface $quarto_repository
    ) {
        $this->pedido_repository = $pedido_repository;
        $this->quarto_repository = $quarto_repository;
    }

    /**
     * @param SalvarPedidoCommand $command
     * @return Pedido
     * @throws PedidoInvalidoException
     * @throws Exception
     */
    public function handle(SalvarPedidoCommand $command): Pedido
    {
        $cpf = new CPF($command->getCpf());
        $valor_total = $this->calcularValorTotal($command->getItens());

        if ($valor_total === 0) {
            throw PedidoInvalidoException::valorTotalZerado();
        }

        $pedido = new Pedido();
        $pedido->setNome($command->getNome());
        $pedido->setCpf($cpf);
        $pedido->setEmail($command->getEmail());
        $pedido->setTelefone($command->getTelefone());
        $pedido->setValorTotal($valor_total);
        $pedido->setItens($command->getItens());

        $this->pedido_repository->create($pedido);

        return $pedido;
    }

    /**
     * Calcular o valor total do pedido
     * @param array $itens
     * @return float
     * @throws Exception
     */
    private function calcularValorTotal(array $itens): float
    {
        $valor_total = 0.;

        foreach ($itens as $item) {
            /** @var Quarto $quarto */
            $quarto = $this->quarto_repository->find($item->quartoID);
            $checkin = new DateTime($item->checkin);
            $checkout = new DateTime($item->checkout);
            $qtde_hospedes = $item->adultos + $item->criancas;

            $quarto->isDisponivelPeriodo($checkin, $checkout);
            $quarto->getDispon($checkin, $checkout)->map(function (Disponibilidade $dispon) use ($qtde_hospedes, &$valor_total) {
                $valor_total += $dispon->getValorPorQtdePessoas($qtde_hospedes);
            });
        }

        return $valor_total;
    }
}