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

namespace Reservas\PainelDLX\Tests\UseCases\Pedidos\ConfirmarPgtoPedido;

use DateTime;
use DLX\Infra\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\ORMException;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\PainelDLX\Domain\Entities\Pedido;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Repositories\PedidoRepositoryInterface;
use Reservas\PainelDLX\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\PainelDLX\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommand;
use Reservas\PainelDLX\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommandHandler;
use Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand;
use Reservas\PainelDLX\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler;
use Reservas\Tests\Helpers\QuartoTesteHelper;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ConfirmarPgtoPedidoCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Pedidos\ConfirmarPgtoPedido
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommandHandler
 */
class ConfirmarPgtoPedidoCommandHandlerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return ConfirmarPgtoPedidoCommandHandler
     * @throws ORMException
     */
    public function test__construct(): ConfirmarPgtoPedidoCommandHandler
    {
        /** @var PedidoRepositoryInterface $pedido_repository */
        $pedido_repository = EntityManagerX::getRepository(Pedido::class);
        $handler = new ConfirmarPgtoPedidoCommandHandler($pedido_repository);

        $this->assertInstanceOf(ConfirmarPgtoPedidoCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param ConfirmarPgtoPedidoCommandHandler $handler
     * @throws DBALException
     * @throws ORMException
     * @throws \Exception
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_deve_salvar_Pedido_como_pago_e_criar_Reservas(ConfirmarPgtoPedidoCommandHandler $handler)
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);

        /** @var \Reservas\PainelDLX\Domain\Quartos\Entities\Quarto $quarto1 */
        $quarto1 = QuartoTesteHelper::getRandom();
        $checkin1 = new DateTime();
        $checkout1 = (clone $checkin1)->modify('+1 day');

        /** @var \Reservas\PainelDLX\Domain\Quartos\Entities\Quarto $quarto2 */
        $quarto2 = QuartoTesteHelper::getRandom();
        $checkin2 = (new DateTime())->modify('+7 days');
        $checkout2 = (clone $checkin1)->modify('+1 day');

        $pedido = (new Pedido())
            ->setNome('Teste de Cliente')
            ->setCpf('000.000.000-00')
            ->setTelefone('(00) 0 0000-0000')
            ->setEmail('cliente@gmail.com')
            ->setValorTotal(1234);

        $pedido->addItem($quarto1, $checkin1->format('Y-m-d'), $checkout1->format('Y-m-d'), 2, 0, 616);
        $pedido->addItem($quarto2, $checkin2->format('Y-m-d'), $checkout2->format('Y-m-d'), 2, 1, 616);

        /** @var PedidoRepositoryInterface $pedido_repository */
        $pedido_repository = EntityManagerX::getRepository(Pedido::class);
        $pedido_repository->create($pedido);

        $command = new GerarReservasPedidoCommand($pedido);
        (new GerarReservasPedidoCommandHandler($quarto_repository))->handle($command);

        $command = new ConfirmarPgtoPedidoCommand($pedido);
        $handler->handle($command);

        $has_reservas_sem_id = $pedido->getReservas()->exists(function ($key, Reserva $reserva) {
            return is_null($reserva->getId());
        });

        $this->assertNotNull($pedido->getId());
        $this->assertFalse($has_reservas_sem_id);

        // Vefificar se as reservas foram salvas no banco de dados
        $query = '
            select
                *
            from
                dlx_reservas_cadastro
            where 
                reserva_pedido = :pedido_id
        ';

        $con = EntityManagerX::getInstance()->getConnection();

        $sql = $con->prepare($query);
        $sql->bindValue(':pedido_id', $pedido->getId(), ParameterType::INTEGER);
        $sql->execute();

        $this->assertEquals($pedido->getReservas()->count(), $sql->rowCount());
    }
}
