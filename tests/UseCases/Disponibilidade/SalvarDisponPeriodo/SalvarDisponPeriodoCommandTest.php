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

namespace Reservas\Tests\UseCases\Disponibilidade\SalvarDisponPeriodo;

use DateTime;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommand;
use Reservas\Tests\ReservasTestCase;

/**
 * Class SalvarDisponPeriodoCommandTest
 * @package Reservas\Tests\UseCases\Disponibilidade\SalvarDisponPeriodo
 * @coversDefaultClass \Reservas\UseCases\Disponibilidade\SalvarDisponPeriodo\SalvarDisponPeriodoCommand
 */
class SalvarDisponPeriodoCommandTest extends ReservasTestCase
{

    /**
     * @throws \Exception
     */
    public function test__construct(): SalvarDisponPeriodoCommand
    {
        $data_inicial = new DateTime();
        $data_final = (new DateTime())->modify('+2 days');

        $quarto = new Quarto('Teste', 1, 10);

        $command = new SalvarDisponPeriodoCommand(
            $data_inicial,
            $data_final,
            $quarto,
            1,
            [12.34]
        );

        $this->assertInstanceOf(SalvarDisponPeriodoCommand::class, $command);

        return $command;
    }

    /**
     * @param SalvarDisponPeriodoCommand $command
     * @covers ::getDataInicial
     * @depends test__construct
     */
    public function test_GetDataInicial(SalvarDisponPeriodoCommand $command)
    {
        $this->assertInstanceOf(DateTime::class, $command->getDataInicial());
    }

    /**
     * @param SalvarDisponPeriodoCommand $command
     * @covers ::getDataFinal
     * @depends test__construct
     */
    public function test_GetDataFinal(SalvarDisponPeriodoCommand $command)
    {
        $this->assertInstanceOf(DateTime::class, $command->getDataFinal());
    }

    /**
     * @param SalvarDisponPeriodoCommand $command
     * @covers ::getQuarto
     * @depends test__construct
     */
    public function test_GetQuarto(SalvarDisponPeriodoCommand $command)
    {
        $this->assertInstanceOf(Quarto::class, $command->getQuarto());
    }

    /**
     * @param SalvarDisponPeriodoCommand $command
     * @covers ::getQtde
     * @depends test__construct
     */
    public function test_GetQtde(SalvarDisponPeriodoCommand $command)
    {
        $this->assertIsInt($command->getQtde());
    }

    /**
     * @param SalvarDisponPeriodoCommand $command
     * @covers ::getValores
     * @depends test__construct
     */
    public function testGetValores(SalvarDisponPeriodoCommand $command)
    {
        $has_valores_invalidos = array_filter($command->getValores(), function ($valor) {
            return !is_float($valor);
        });

        $this->assertIsArray($command->getValores());
        $this->assertEmpty($has_valores_invalidos);
    }
}
