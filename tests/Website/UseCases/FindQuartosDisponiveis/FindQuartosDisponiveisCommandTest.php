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

namespace Reservas\Website\Tests\UseCases\FindQuartosDisponiveis;

use DateTime;
use Reservas\Tests\ReservasTestCase;
use Reservas\Website\UseCases\FindQuartosDisponiveis\FindQuartosDisponiveisCommand;

/**
 * Class FindQuartosDisponiveisCommandTest
 * @package Reservas\Website\Tests\UseCases\FindQuartosDisponiveis
 * @coversDefaultClass \Reservas\Website\UseCases\FindQuartosDisponiveis\FindQuartosDisponiveisCommand
 */
class FindQuartosDisponiveisCommandTest extends ReservasTestCase
{
    /**
     * @return FindQuartosDisponiveisCommand
     * @throws \Exception
     */
    public function test__construct(): FindQuartosDisponiveisCommand
    {
        $command = new FindQuartosDisponiveisCommand(
            new DateTime(),
            new DateTime(),
            2,
            0,
            1
        );
        $this->assertInstanceOf(FindQuartosDisponiveisCommand::class, $command);

        return $command;
    }

    /**
     * @param FindQuartosDisponiveisCommand $command
     * @covers ::getQtdeCriancas
     * @depends test__construct
     */
    public function test_GetDataEntrada(FindQuartosDisponiveisCommand $command)
    {
        $this->assertInstanceOf(DateTime::class, $command->getDataEntrada());
    }

    /**
     * @param FindQuartosDisponiveisCommand $command
     * @covers ::getQtdeCriancas
     * @depends test__construct
     */
    public function test_GetDataSaida(FindQuartosDisponiveisCommand $command)
    {
        $this->assertInstanceOf(DateTime::class, $command->getDataSaida());
    }

    /**
     * @param FindQuartosDisponiveisCommand $command
     * @covers ::getQtdeCriancas
     * @depends test__construct
     */
    public function test_GetQtdeAdultos(FindQuartosDisponiveisCommand $command)
    {
        $this->assertIsInt($command->getQtdeAdultos());
    }

    /**
     * @param FindQuartosDisponiveisCommand $command
     * @covers ::getQtdeCriancas
     * @depends test__construct
     */
    public function test_GetQtdeCriancas(FindQuartosDisponiveisCommand $command)
    {
        $this->assertIsInt($command->getQtdeCriancas());
    }

    /**
     * @param FindQuartosDisponiveisCommand $command
     * @covers ::getQtdeCriancas
     * @depends test__construct
     */
    public function test_GetQtdeQuartos(FindQuartosDisponiveisCommand $command)
    {
        $this->assertIsInt($command->getQtdeQuartos());
    }
}
