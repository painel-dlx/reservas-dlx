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

namespace Reservas\Tests\UseCases\Quartos\FindQuartosDisponiveis;

use Reservas\UseCases\Quartos\FindQuartosDisponiveis\FindQuartosDisponiveisCommand;
use PHPUnit\Framework\TestCase;

/**
 * Class FindQuartosDisponiveisCommandTest
 * @package Reservas\Tests\UseCases\Quartos\FindQuartosDisponiveis
 * @coversDefaultClass \Reservas\UseCases\Quartos\FindQuartosDisponiveis\FindQuartosDisponiveisCommand
 */
class FindQuartosDisponiveisCommandTest extends TestCase
{
    public function test__construct(): FindQuartosDisponiveisCommand
    {
        $data = new \DateTime();

        $command = new FindQuartosDisponiveisCommand($data, $data, 1,1);
        $this->assertInstanceOf(FindQuartosDisponiveisCommand::class, $command);

        return $command;
    }

    /**
     * @param FindQuartosDisponiveisCommand $command
     * @covers ::getCheckin
     * @depends test__construct
     */
    public function test_GetCheckin(FindQuartosDisponiveisCommand $command)
    {
        $this->assertInstanceOf(\DateTime::class, $command->getCheckin());
    }

    /**
     * @param FindQuartosDisponiveisCommand $command
     * @covers ::getCheckout
     * @depends test__construct
     */
    public function test_GetCheckout(FindQuartosDisponiveisCommand $command)
    {
        $this->assertInstanceOf(\DateTime::class, $command->getCheckout());
    }

    /**
     * @param FindQuartosDisponiveisCommand $command
     * @covers ::getQtdeHospedes
     * @depends test__construct
     */
    public function test_GetQtdeHospedes(FindQuartosDisponiveisCommand $command)
    {
        $this->assertIsInt($command->getQtdeHospedes());
    }

    /**
     * @param FindQuartosDisponiveisCommand $command
     * @covers ::getQdeQuartos
     * @depends test__construct
     */
    public function test_GetQdeQuartos(FindQuartosDisponiveisCommand $command)
    {
        $this->assertIsInt($command->getQdeQuartos());
    }
}
