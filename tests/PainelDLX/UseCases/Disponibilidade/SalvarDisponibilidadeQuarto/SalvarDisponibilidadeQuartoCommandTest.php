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

namespace Reservas\PainelDLX\Tests\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto;

use DateTime;
use Reservas\PainelDLX\Domain\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Tests\ReservasTestCase;
use Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommand;

/**
 * Class SalvarDisponibilidadeQuartoCommandTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Disponibilidade\SalvarDisponibilidadeQuarto\SalvarDisponibilidadeQuartoCommand
 */
class SalvarDisponibilidadeQuartoCommandTest extends ReservasTestCase
{

    /**
     * @throws \Exception
     * @covers ::getDisponibilidade
     */
    public function test_GetDisponibilidade()
    {
        $quarto = new Quarto('Teste', 1, 10);
        $data = new DateTime();

        $disponibilidade = new Disponibilidade($quarto, $data, 10);

        $command = new SalvarDisponibilidadeQuartoCommand($disponibilidade);
        $this->assertInstanceOf(Disponibilidade::class, $command->getDisponibilidade());
        $this->assertEquals($disponibilidade, $command->getDisponibilidade());
    }
}
