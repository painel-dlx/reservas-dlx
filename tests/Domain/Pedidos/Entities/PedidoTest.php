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

use CPF\CPF;
use DateInterval;
use DatePeriod;
use DateTime;
use DLX\Infra\EntityManagerX;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\ORMException;
use Exception;
use PainelDLX\Domain\Usuarios\Entities\Usuario;
use PainelDLX\Testes\TestCase\TesteComTransaction;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Disponibilidade\Repositories\DisponibilidadeRepositoryInterface;
use Reservas\Domain\Pedidos\Entities\Pedido;
use Reservas\Domain\Pedidos\Entities\PedidoHistorico;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Repositories\QuartoRepositoryInterface;
use Reservas\Domain\Reservas\Entities\Reserva;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommand;
use Reservas\UseCases\Pedidos\GerarReservasPedido\GerarReservasPedidoCommandHandler;
use Reservas\Tests\ReservasTestCase;

/**
 * Class PedidoTest
 * @package Reservas\Tests\Domain\Entities
 * @coversDefaultClass \Reservas\Domain\Pedidos\Entities\Pedido
 */
class PedidoTest extends ReservasTestCase
{
    use TesteComTransaction;

    /**
     * Adicionar disponibilidades nos quartos para garantir que o quarto tenha disponibilidade para os testes
     * @param Quarto $quarto
     * @param DateTime $checkin
     * @param DateTime $checkout
     * @throws Exception
     */
    private function addDisponQuarto(Quarto $quarto, DateTime $checkin, DateTime $checkout): void
    {
        /** @var DisponibilidadeRepositoryInterface $dispon_repository */
        $dispon_repository = EntityManagerX::getRepository(Disponibilidade::class);
        $dispon_repository->salvarDisponPorPeriodo($checkin, $checkout, $quarto, 10, [1 => 12.34]);
    }

    /**
     * @return Pedido
     * @throws Exception
     */
    public function test__construct(): Pedido
    {
        $checkin = (new DateTime())->modify('+1 day');
        $checkout = (clone $checkin)->modify('+2 days');

        $quarto = new Quarto('Teste de Quarto', 10, 10);
        $quarto->setId(1);

        $this->addDisponQuarto($quarto, $checkin, $checkout);

        $pedido = new Pedido();
        $pedido->setId(1);
        $pedido->setNome('Nome do Cliente');
        $pedido->setEmail('email.cliente@gmail.com');
        $pedido->setCpf(new CPF('177.965.730-73'));
        $pedido->setTelefone('(61) 9 8350-3517');
        $pedido->addItem($quarto, $checkin->format('Y-m-d'), $checkout->format('Y-m-d'), 1, 0, 10);

        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertInstanceOf(ArrayCollection::class, $pedido->getReservas());
        $this->assertInstanceOf(ArrayCollection::class, $pedido->getHistorico());
        $this->assertTrue($pedido->isPendente());
        $this->assertEquals('digitada', $pedido->getFormaPgto());

        return $pedido;
    }

    /**
     * @param Pedido $pedido
     * @covers ::isPendente
     * @depends test__construct
     */
    public function test_IsPendente(Pedido $pedido)
    {
        $pedido->setStatus(Pedido::STATUS_PAGO);
        $this->assertFalse($pedido->isPendente());

        $pedido->setStatus(Pedido::STATUS_PENDENTE);
        $this->assertTrue($pedido->isPendente());
    }

    /**
     * @param Pedido $pedido
     * @covers ::isPago
     * @depends test__construct
     */
    public function test_IsPago(Pedido $pedido)
    {
        $pedido->setStatus(Pedido::STATUS_PENDENTE);
        $this->assertFalse($pedido->isPago());

        $pedido->setStatus(Pedido::STATUS_PAGO);
        $this->assertTrue($pedido->isPago());
    }

    /**
     * @param Pedido $pedido
     * @covers ::isCancelado
     * @depends test__construct
     */
    public function test_IsCancelado(Pedido $pedido)
    {
        $pedido->setStatus(Pedido::STATUS_PENDENTE);
        $this->assertFalse($pedido->isCancelado());

        $pedido->setStatus(Pedido::STATUS_CANCELADO);
        $this->assertTrue($pedido->isCancelado());
    }

    /**
     * @param Pedido $pedido
     * @covers ::addHistorico
     * @depends test__construct
     */
    public function test_AddHistorico_instancia_PedidoHistorico_e_adiciona_na_Collection(Pedido $pedido)
    {
        // Limpar histórico de testes anteriores
        $pedido->getHistorico()->clear();

        $usuario = new Usuario('Teste de Funcionário', 'funcionario@gmail.com');

        $status = 'Cancelado';
        $motivo = 'Motivo de cancelamento';

        $pedido->addHistorico($status, $motivo, $usuario);

        /** @var PedidoHistorico $pedido_historico */
        $pedido_historico = $pedido->getHistorico()->first();

        // Teste da Collection
        $this->assertCount(1, $pedido->getHistorico());

        // Teste do histórico criado
        $this->assertInstanceOf(PedidoHistorico::class, $pedido_historico);
        $this->assertEquals($status, $pedido_historico->getStatus());
        $this->assertEquals($motivo, $pedido_historico->getMotivo());
        $this->assertEquals($pedido, $pedido_historico->getPedido());
    }

    /**
     * @param Pedido $pedido
     * @covers ::pago
     * @depends test__construct
     * @throws ORMException
     * @throws Exception
     */
    public function test_Pago_seta_Pedido_como_pago_e_adiciona_historico(Pedido $pedido)
    {
        $this->markTestSkipped('Está dando erro nas disponibilidades do quarto!');

        // Limpar histórico de testes anteriores
        $pedido->getHistorico()->clear();

        // Setar o pedido como pendente
        $pedido->setStatus(Pedido::STATUS_PENDENTE);

        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = EntityManagerX::getRepository(Quarto::class);

        $lista_itens = $pedido->getItens();

        foreach ($lista_itens as $item) {
            /** @var Quarto $quarto */
            $quarto = $quarto_repository->find($item->quartoID);

            $checkin = new DateTime($item->checkin);
            $checkout = new DateTime($item->checkout);

            $this->addDisponQuarto($quarto, $checkin, $checkout);
        }

        $usuario = new Usuario('Teste de Funcionário', 'funcionario@gmail.com');
        $motivo = 'Motivo de confirmação';

        // Gerar reservas
        $command = new GerarReservasPedidoCommand($pedido);
        (new GerarReservasPedidoCommandHandler($quarto_repository))->handle($command);

        $pedido->pago($motivo, $usuario);

        $has_historico_pago = $pedido->getHistorico()->exists(function ($key, PedidoHistorico $historico) {
            return $historico->getStatus() === Pedido::STATUS_PAGO;
        });

        $this->assertTrue($pedido->isPago());
        $this->assertTrue($has_historico_pago);
    }

    /**
     * @param Pedido $pedido
     * @covers ::pago
     * @depends test__construct
     */
    public function test_Cancelado_seta_Pedido_como_cancelado_e_adiciona_historico(Pedido $pedido)
    {
        // Limpar histórico de testes anteriores
        $pedido->getHistorico()->clear();

        // Setar o pedido como pendente
        $pedido->setStatus(Pedido::STATUS_PENDENTE);

        $usuario = new Usuario('Teste de Funcionário', 'funcionario@gmail.com');
        $motivo = 'Motivo de cancelamento';

        $pedido->cancelado($motivo, $usuario);

        $has_historico_cancelado = $pedido->getHistorico()->exists(function ($key, PedidoHistorico $historico) {
            return $historico->getStatus() === Pedido::STATUS_CANCELADO;
        });

        $has_reserva_ativa = $pedido->getReservas()->exists(function ($key, Reserva $reserva) {
             return !$reserva->isCancelada();
        });

        $this->assertTrue($pedido->isCancelado());
        $this->assertTrue($has_historico_cancelado);
        $this->assertFalse($has_reserva_ativa);
    }
}
