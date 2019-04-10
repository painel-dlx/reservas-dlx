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

namespace Reservas\PainelDLX\Tests\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto;

use DateTime;
use PainelDLX\Testes\PainelDLXTests;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand;

/**
 * Class GetDisponibilidadePorDataQuartoCommandTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Disponibilidade\GetDisponibilidadePorDataQuarto\GetDisponibilidadePorDataQuartoCommand
 */
class GetDisponibilidadePorDataQuartoCommandTest extends PainelDLXTests
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
