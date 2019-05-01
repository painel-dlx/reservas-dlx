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

namespace Reservas\PainelDLX\Tests\UseCases\Clientes\MostrarCpfCompletoPedido;

use DLX\Infra\EntityManagerX;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use PainelDLX\Testes\Helpers\UsuarioTesteHelper;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\PainelDLX\Domain\Entities\Pedido;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use Reservas\PainelDLX\Domain\Entities\VisualizacaoCpf;
use Reservas\PainelDLX\Domain\Repositories\PedidoRepositoryInterface;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommand;
use Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommandHandler;
use PHPUnit\Framework\TestCase;
use Reservas\Tests\Helpers\PedidoTesteHelper;
use Reservas\Tests\Helpers\ReservasUsuarioTesteHelper;
use Reservas\Tests\ReservasTestCase;

/**
 * Class MostrarCpfCompletoPedidoCommandHandlerTest
 * @package Reservas\PainelDLX\Tests\UseCases\Clientes\MostrarCpfCompletoPedido
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Clientes\MostrarCpfCompletoPedido\MostrarCpfCompletoPedidoCommandHandler
 */
class MostrarCpfCompletoPedidoCommandHandlerTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * @return MostrarCpfCompletoPedidoCommandHandler
     * @throws ORMException
     */
    public function test__construct(): MostrarCpfCompletoPedidoCommandHandler
    {
        /** @var PedidoRepositoryInterface $pedido_repository */
        $pedido_repository = EntityManagerX::getRepository(Pedido::class);
        $handler = new MostrarCpfCompletoPedidoCommandHandler($pedido_repository);

        $this->assertInstanceOf(MostrarCpfCompletoPedidoCommandHandler::class, $handler);

        return $handler;
    }

    /**
     * @param MostrarCpfCompletoPedidoCommandHandler $handler
     * @throws DBALException
     * @throws ORMException
     * @covers ::handle
     * @depends test__construct
     */
    public function test_Handle_retornar_cpf_completo_e_adicionar_visualizacao_de_cpf_em_todas_reservas(MostrarCpfCompletoPedidoCommandHandler $handler)
    {
        $pedido = PedidoTesteHelper::getRandom();
        $usuario = ReservasUsuarioTesteHelper::getUsuarioRandom();

        $command = new MostrarCpfCompletoPedidoCommand($pedido, $usuario);
        $cpf = $handler->handle($command);

        $this->assertIsString($cpf);

        $pedido->getReservas()->map(function (Reserva $reserva) {
            $this->assertTrue($reserva->getVisualizacoesCpf()->count() > 0);
        });
    }
}
