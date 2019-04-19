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
use Reservas\PainelDLX\Domain\Entities\ReservaHistorico;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ReservaHistoricoTest
 * @package Reservas\PainelDLX\Tests\Domain\Entities
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Entities\ReservaHistorico
 */
class ReservaHistoricoTest extends ReservasTestCase
{
    /**
     * @return ReservaHistorico
     */
    public function test__construct(): ReservaHistorico
    {
        $status = 'Confirmada';
        $motivo = 'Teste de motivo para confirmar uma reserva.';
        $historico = new ReservaHistorico($status, $motivo);

        $this->assertInstanceOf(ReservaHistorico::class, $historico);
        $this->assertEquals($status, $historico->getStatus());
        $this->assertEquals($motivo, $historico->getMotivo());
        $this->assertEquals(new DateTime(), $historico->getData(), '', .1);

        return $historico;
    }
}
