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

namespace Reservas\Tests\UseCases\Reservas\FiltrarReservasPorPeriodo;

use PHPUnit\Framework\TestCase;
use Reservas\UseCases\Reservas\FiltrarReservasPorPeriodo\FiltrarReservasPorPeriodoCommand;

/**
 * Class FiltrarReservasPorPeriodoCommandTest
 * @package Reservas\UseCases\Reservas\FiltrarReservasPorPeriodo
 * @coversDefaultClass \Reservas\UseCases\Reservas\FiltrarReservasPorPeriodo\FiltrarReservasPorPeriodoCommand
 */
class FiltrarReservasPorPeriodoCommandTest extends TestCase
{
    /**
     * @return FiltrarReservasPorPeriodoCommand
     */
    public function test__construct(): FiltrarReservasPorPeriodoCommand
    {
        $lista_reservas = [];
        $data_atual = date('Y-m-d');

        $command = new FiltrarReservasPorPeriodoCommand($lista_reservas, $data_atual, $data_atual);
        $this->assertInstanceOf(FiltrarReservasPorPeriodoCommand::class, $command);

        return $command;
    }

    /**
     * @param FiltrarReservasPorPeriodoCommand $command
     * @covers ::getListaReservas
     * @depends test__construct
     */
    public function test_GetListaReservas(FiltrarReservasPorPeriodoCommand $command)
    {
        $this->assertIsArray($command->getListaReservas());
    }

    /**
     * @param FiltrarReservasPorPeriodoCommand $command
     * @covers ::getDataInicial
     * @depends test__construct
     */
    public function test_GetDataInicial(FiltrarReservasPorPeriodoCommand $command)
    {
        $this->assertIsString($command->getDataInicial());
    }

    /**
     * @param FiltrarReservasPorPeriodoCommand $command
     * @covers ::getDataFinal
     * @depends test__construct
     */
    public function test_GetDataFinal(FiltrarReservasPorPeriodoCommand $command)
    {
        $this->assertIsString($command->getDataFinal());
    }
}
