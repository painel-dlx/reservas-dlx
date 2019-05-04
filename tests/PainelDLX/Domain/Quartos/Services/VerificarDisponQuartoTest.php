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

namespace Reservas\PainelDLX\Tests\Domain\Quartos\Services;

use DateInterval;
use DatePeriod;
use DateTime;
use Reservas\PainelDLX\Domain\Exceptions\QuartoNaoDisponivelException;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use Reservas\PainelDLX\Domain\Quartos\Exceptions\VerificarDisponQuartoException;
use Reservas\PainelDLX\Domain\Quartos\Services\VerificarDisponQuarto;
use Reservas\Tests\ReservasTestCase;

/**
 * Class VerificarDisponQuartoTest
 * @package Reservas\PainelDLX\Domain\Quartos\Services
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Quartos\Services\VerificarDisponQuarto
 */
class VerificarDisponQuartoTest extends ReservasTestCase
{
    /**
     * @throws VerificarDisponQuartoException
     * @throws QuartoNaoDisponivelException
     * @covers ::executar
     */
    public function test_Executar_deve_lancar_excecao_quando_nao_encontrar_nenhuma_disponibilidade()
    {
        $quarto = new Quarto('Teste de Quarto', 10, 10);
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+5 days');

        $this->expectException(VerificarDisponQuartoException::class);
        $this->expectExceptionCode(10);

        (new VerificarDisponQuarto())->executar($quarto, $checkin, $checkout);
    }

    /**
     * @throws VerificarDisponQuartoException
     * @throws QuartoNaoDisponivelException
     * @throws \Reservas\PainelDLX\Domain\Exceptions\ValorMenorQueMinimoQuartoException
     * @covers ::executar
     */
    public function test_Executar_deve_lancar_excecao_quando_alguma_disponibilidade_nao_for_valida()
    {
        $quarto = new Quarto('Teste de Quarto', 10, 10);
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+1 days');

        $dt_interval = new DateInterval('P1D');
        $dt_periodo = new DatePeriod($checkin, $dt_interval, $checkout, DatePeriod::EXCLUDE_START_DATE);

        /** @var DateTime $data */
        foreach ($dt_periodo as $data) {
            $quarto->addDispon($data, 1, [1 => 10]);
        }

        $this->expectException(VerificarDisponQuartoException::class);
        $this->expectExceptionCode(10);

        (new VerificarDisponQuarto())->executar($quarto, $checkin, $checkout);
    }

    /**
     * @throws VerificarDisponQuartoException
     * @throws QuartoNaoDisponivelException
     * @throws \Reservas\PainelDLX\Domain\Exceptions\ValorMenorQueMinimoQuartoException
     * @covers ::executar
     */
    public function test_Executar_deve_retornar_true_quando_quarto_estiver_disponivel_para_todos_os_dias()
    {
        $quarto = new Quarto('Teste de Quarto', 10, 10);
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+1 days');

        $dt_interval = new DateInterval('P1D');
        $dt_periodo = new DatePeriod($checkin, $dt_interval, $checkout);

        /** @var DateTime $data */
        foreach ($dt_periodo as $data) {
            $quarto->addDispon($data, 1, [1 => 10]);
        }

        $is_disponivel = (new VerificarDisponQuarto())->executar($quarto, $checkin, $checkout);
        $this->assertTrue($is_disponivel);
    }
}
