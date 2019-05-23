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

namespace Reservas\Tests\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto;

use DateTime;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand;
use Reservas\Tests\ReservasTestCase;

/**
 * Class GetDisponibilidadePorDataQuartoCommandTest
 * @package Reservas\Tests\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto
 * @coversDefaultClass \Reservas\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand
 */
class GetDisponibilidadePorDataQuartoCommandTest extends ReservasTestCase
{
    /**
     * @covers ::__construct
     * @throws \Exception
     */
    public function test__construct(): GetDisponibilidadePorDataQuartoCommand
    {
        $quarto = new Quarto('Teste', 10, 10);

        $command = new GetDisponibilidadePorDataQuartoCommand($quarto, new DateTime());
        $this->assertInstanceOf(GetDisponibilidadePorDataQuartoCommand::class, $command);

        return $command;
    }

    /**
     * @covers ::getData
     * @param GetDisponibilidadePorDataQuartoCommand $command
     * @depends test__construct
     */
    public function test_GetData(GetDisponibilidadePorDataQuartoCommand $command)
    {
        $this->assertInstanceOf(DateTime::class, $command->getData());
    }

    /**
     * @covers ::getQuarto
     * @param GetDisponibilidadePorDataQuartoCommand $command
     * @depends test__construct
     */
    public function test_GetQuarto(GetDisponibilidadePorDataQuartoCommand $command)
    {
        $this->assertInstanceOf(Quarto::class, $command->getQuarto());
    }
}
