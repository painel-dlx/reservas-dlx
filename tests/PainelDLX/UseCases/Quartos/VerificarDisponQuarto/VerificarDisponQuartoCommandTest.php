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

namespace Reservas\PainelDLX\Tests\UseCases\Quartos\VerificarDisponQuarto;

use DateTime;
use Exception;
use Reservas\PainelDLX\Domain\Quartos\Entities\Quarto;
use PHPUnit\Framework\TestCase;
use Reservas\PainelDLX\UseCases\Quartos\VerificarDisponQuarto\VerificarDisponQuartoCommand;

/**
 * Class VerificarDisponQuartoCommandTest
 * @package Reservas\Website\Tests\UseCases\VerificarDisponQuarto
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Quartos\VerificarDisponQuarto\VerificarDisponQuartoCommand
 */
class VerificarDisponQuartoCommandTest extends TestCase
{
    /**
     * @return VerificarDisponQuartoCommand
     * @throws Exception
     */
    public function test__construct(): VerificarDisponQuartoCommand
    {
        $quarto = new Quarto('Teste de Quarto', 10, 10);
        $checkin = new DateTime();
        $checkout = (clone $checkin)->modify('+1 day');

        $command = new VerificarDisponQuartoCommand($quarto, $checkin, $checkout, 1);

        $this->assertInstanceOf(VerificarDisponQuartoCommand::class, $command);

        return $command;
    }

    /**
     * @param VerificarDisponQuartoCommand $command
     * @covers ::getQuarto
     * @depends test__construct
     */
    public function test_GetQuarto(VerificarDisponQuartoCommand $command)
    {
        $this->assertInstanceOf(Quarto::class, $command->getQuarto());
    }

    /**
     * @param VerificarDisponQuartoCommand $command
     * @covers ::getCheckin
     * @depends test__construct
     */
    public function test_GetCheckin(VerificarDisponQuartoCommand $command)
    {
        $this->assertInstanceOf(DateTime::class, $command->getCheckin());
    }

    /**
     * @param VerificarDisponQuartoCommand $command
     * @covers ::getCheckout
     * @depends test__construct
     */
    public function test_GetCheckout(VerificarDisponQuartoCommand $command)
    {
        $this->assertInstanceOf(DateTime::class, $command->getCheckout());
    }

    /**
     * @param VerificarDisponQuartoCommand $command
     * @covers ::getHospedes
     * @depends test__construct
     */
    public function test_GetHospedes(VerificarDisponQuartoCommand $command)
    {
        $this->assertIsInt($command->getHospedes());
    }

    /**
     * @param VerificarDisponQuartoCommand $command
     * @covers ::getQuarto
     * @depends test__construct
     */
    public function test_GetQtdeQuartos(VerificarDisponQuartoCommand $command)
    {
        $this->assertIsInt($command->getQtdeQuartos());
    }
}
