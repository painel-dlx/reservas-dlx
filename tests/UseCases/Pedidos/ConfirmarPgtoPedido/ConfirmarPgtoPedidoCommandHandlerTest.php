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

namespace Reservas\Tests\UseCases\Pedidos\ConfirmarPgtoPedido;

use CPF\CPF;
use DateInterval;
use DatePeriod;
use DateTime;
use DLX\Infrastructure\EntityManagerX;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\ORMException;
use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Tests\TestCase\TesteComTransaction;
use PHPUnit\Framework\TestCase;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\Domain\Pedidos\Repositories\PedidoRepositoryInterface;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommand;
use Reservas\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommandHandler;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler;
use Reservas\Tests\Helpers\QuartoTesteHelper;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ConfirmarPgtoPedidoCommandHandlerTest
 * @package Reservas\Tests\UseCases\Pedidos\ConfirmarPgtoPedido
 * @coversDefaultClass \Reservas\UseCases\Pedidos\ConfirmarPgtoPedido\ConfirmarPgtoPedidoCommandHandler
 */
class ConfirmarPgtoPedidoCommandHandlerTest extends TestCase
{
    /**
     * @throws Exception
     * @covers ::handle
     */
    public function test_Handle_deve_configurar_Pedido_como_pago_e_criar_Reservas()
    {
        $quarto1 = $this->createMock(Quarto::class);
        $quarto1->method('getDisponibilidade')->willReturn(new ArrayCollection());
        $quarto1->method('isDisponivelPeriodo')->willReturn(true);

        $quarto2 = $this->createMock(Quarto::class);
        $quarto2->method('getDisponibilidade')->willReturn(new ArrayCollection());
        $quarto2->method('isDisponivelPeriodo')->willReturn(true);

        $checkin1 = new DateTime();
        $checkout1 = (clone $checkin1)->modify('+1 day');

        $checkin2 = (new DateTime())->modify('+7 days');
        $checkout2 = (clone $checkin1)->modify('+1 day');

        $usuario = $this->createMock(Usuario::class);
        $usuario->method('getId')->willReturn(mt_rand());
        $usuario->method('getNome')->willReturn('Teste de Usuário');

        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('find')->willReturnOnConsecutiveCalls($quarto1, $quarto2);

        $pedido_repository = $this->createMock(PedidoRepositoryInterface::class);
        $pedido_repository->method('update')->willReturn(null);

        /** @var Quarto $quarto1 */
        /** @var Quarto $quarto2 */
        /** @var Usuario $usuario */
        /** @var QuartoRepositoryInterface $quarto_repository */
        /** @var PedidoRepositoryInterface $pedido_repository */

        $pedido = (new Pedido())
            ->setNome('Teste de Cliente')
            ->setCpf(new CPF('000.000.000-00'))
            ->setTelefone('(00) 0 0000-0000')
            ->setEmail('cliente@gmail.com')
            ->setValorTotal(1234);

        $pedido->addItem($quarto1, $checkin1, $checkout1, 2, 0, 616);
        $pedido->addItem($quarto2, $checkin2, $checkout2, 2, 1, 616);

        $command = new GerarReservasPedidoCommand($pedido);
        (new GerarReservasPedidoCommandHandler($quarto_repository))->handle($command);

        $command = new ConfirmarPgtoPedidoCommand($pedido, 'Teste Unitário', $usuario);
        (new ConfirmarPgtoPedidoCommandHandler($pedido_repository))->handle($command);

        $this->assertTrue($pedido->isPago());
    }
}
