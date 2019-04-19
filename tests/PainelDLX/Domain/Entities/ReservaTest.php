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

namespace Reservas\PainelDLX\Tests\Domain\Entities;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Entities\Reserva;
use PHPUnit\Framework\TestCase;
use Reservas\PainelDLX\Domain\Entities\ReservaHistorico;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ReservaTest
 * @package Reservas\PainelDLX\Tests\Domain\Entities
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Entities\Reserva
 */
class ReservaTest extends ReservasTestCase
{

    /**
     * @throws \Exception
     */
    public function test__construct(): Reserva
    {
        $quarto = new Quarto('Teste de Quarto', 1, 10);
        $checkin = new DateTime();
        $checkout = (new DateTime())->modify('+3 days');
        $adultos = mt_rand(1, 3);

        $reserva = new Reserva($quarto, $checkin, $checkout, $adultos);

        $this->assertInstanceOf(Reserva::class, $reserva);
        $this->assertEquals($quarto, $reserva->getQuarto());
        $this->assertEquals($checkin, $reserva->getCheckin());
        $this->assertEquals($checkout, $reserva->getCheckout());
        $this->assertEquals($adultos, $reserva->getAdultos());
        $this->assertInstanceOf(Collection::class, $reserva->getHistorico());

        return $reserva;
    }

    /**
     * @param Reserva $reserva
     * @covers ::addHistorico
     * @depends test__construct
     */
    public function test_AddHistorico_deve_adicionar_um_historico_a_reserva(Reserva $reserva)
    {
        $status = 'Cancelada';
        $motivo = 'Teste de motivo para cancelamento.';

        $reserva->addHistorico($status, $motivo);

        $hasHistoricoAdicionado = $reserva->getHistorico()->exists(function ($key, ReservaHistorico $reserva_historico) use ($reserva, $status, $motivo) {
            return $reserva_historico->getStatus() === $status
                && $reserva_historico->getMotivo() === $motivo
                && $reserva_historico->getReserva() === $reserva;
        });

        $this->assertTrue($hasHistoricoAdicionado);
    }

    /**
     * @param Reserva $reserva
     * @covers ::confirmada
     * @depends test__construct
     */
    public function test_Confirmada_seta_Reserva_como_confirmada(Reserva $reserva)
    {
        $reserva->confirmada('Reserva foi paga via digitação de cartão.');

        $has_historico_confirmada = $reserva->getHistorico()->exists(function ($key, ReservaHistorico $reserva_historico) {
            return $reserva_historico->getStatus() === Reserva::STATUS_CONFIRMADA;
        });

        $this->assertTrue($reserva->isConfirmada());
        $this->assertEquals(Reserva::STATUS_CONFIRMADA, $reserva->getStatus());
        $this->assertTrue($has_historico_confirmada);
    }

    /**
     * @param Reserva $reserva
     * @covers ::cancelada
     * @depends test__construct
     */
    public function test_Cancelada_seta_Reserva_como_cancelada(Reserva $reserva)
    {
        $reserva->cancelada('Reserva foi paga via digitação de cartão.');

        $has_historico_cancelada = $reserva->getHistorico()->exists(function ($key, ReservaHistorico $reserva_historico) {
            return $reserva_historico->getStatus() === Reserva::STATUS_CANCELADA;
        });

        $this->assertTrue($reserva->isCancelada());
        $this->assertEquals(Reserva::STATUS_CANCELADA, $reserva->getStatus());
        $this->assertTrue($has_historico_cancelada);
    }
}
