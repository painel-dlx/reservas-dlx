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

namespace Reservas\Tests\UseCases\Pedidos\GerarReservasPedido;

use CPF\CPF;
use DateTime;
use DLX\Infrastructure\EntityManagerX;
use Doctrine\ORM\ORMException;
use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler;
use Reservas\Tests\Helpers\QuartoTesteHelper;
use Reservas\Tests\ReservasTestCase;

/**
 * Class GerarReservasPedidoCommandHandlerTest
 * @package Reservas\Tests\UseCases\Pedidos\GerarReservasPedido
 * @coversDefaultClass \Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler
 */
class GerarReservasPedidoCommandHandlerTest extends ReservasTestCase
{
    /**
     * @return GerarReservasPedidoCommandHandler
     * @throws ORMException
     */
    public function test__construct(): GerarReservasPedidoCommandHandler
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);
        $handler = new GerarReservasPedidoCommandHandler($quarto_repository);

        $this->assertInstanceOf(GerarReservasPedidoCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param GerarReservasPedidoCommandHandler $handler
     * @covers ::handle
     * @depends test__construct
     * @throws Exception
     */
    public function test_Handle_deve_adicionar_Reservas_no_Pedido_de_acordo_com_itens(GerarReservasPedidoCommandHandler $handler)
    {
        $quarto = QuartoTesteHelper::getRandom();
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+2 days');

        $pedido = new Pedido();
        $pedido->setNome('Teste de Cliente');
        $pedido->setEmail('cliente@gmail.com');
        $pedido->setTelefone('(00) 0 0000-0000');
        $pedido->setCpf(new CPF('000.000.000-00'));
        $pedido->setValorTotal(1234.00);
        $pedido->addItem(
            $quarto,
            $checkin,
            $checkout,
            1,
            1,
            12.34
        );

        $command = new GerarReservasPedidoCommand($pedido);
        $handler->handle($command);

        $this->assertCount(count($pedido->getItens()), $pedido->getReservas());
    }
}
