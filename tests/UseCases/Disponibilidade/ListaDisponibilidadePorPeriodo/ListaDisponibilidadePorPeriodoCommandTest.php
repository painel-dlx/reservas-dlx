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

namespace Reservas\Tests\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo;

use DateTime;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Tests\ReservasTestCase;
use Reservas\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo\ListaDisponibilidadePorPeriodoCommand;


/**
 * Class ListaDisponibilidadePorPeriodoCommandTest
 * @package Reservas\Tests\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo
 * @coversDefaultClass \Reservas\UseCases\Disponibilidade\ListaDisponibilidadePorPeriodo\ListaDisponibilidadePorPeriodoCommand
 */
class ListaDisponibilidadePorPeriodoCommandTest extends ReservasTestCase
{
    /**
     * @return ListaDisponibilidadePorPeriodoCommand
     * @throws \Exception
     */
    public function test__construct(): ListaDisponibilidadePorPeriodoCommand
    {
        $data_inicial = new DateTime();
        $data_final = (clone $data_inicial)->modify('+1 day');

        $quarto = new Quarto('Teste', 10, 10);
        $quarto->setId(1);

        $command = new ListaDisponibilidadePorPeriodoCommand($data_inicial, $data_final, $quarto);

        $this->assertInstanceOf(ListaDisponibilidadePorPeriodoCommand::class, $command);

        return $command;
    }

    /**
     * @param ListaDisponibilidadePorPeriodoCommand $command
     * @covers ::getDataInicio
     * @throws \Exception
     * @depends test__construct
     */
    public function test_GetDataInicio(ListaDisponibilidadePorPeriodoCommand $command)
    {
        $this->assertInstanceOf(DateTime::class, $command->getDataInicio());
        $this->assertEquals(new DateTime(), $command->getDataInicio(), '', 0.1);
    }

    /**
     * @param ListaDisponibilidadePorPeriodoCommand $command
     * @covers ::getDataFinal
     * @throws \Exception
     * @depends test__construct
     */
    public function test_GetDataFinal(ListaDisponibilidadePorPeriodoCommand $command)
    {
        $data_final = (new DateTime())->modify('+1 day');
        $this->assertInstanceOf(DateTime::class, $command->getDataFinal());
        $this->assertEquals($data_final, $command->getDataFinal(), '', 0.1);
    }

    /**
     * @param ListaDisponibilidadePorPeriodoCommand $command
     * @covers ::getQuarto
     * @depends test__construct
     */
    public function test_GetQuarto(ListaDisponibilidadePorPeriodoCommand $command)
    {
        $this->assertInstanceOf(Quarto::class, $command->getQuarto());
    }
}
