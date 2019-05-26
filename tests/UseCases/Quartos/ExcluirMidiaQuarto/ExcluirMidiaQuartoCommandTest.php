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

namespace Reservas\Tests\UseCases\Quartos\ExcluirMidiaQuarto;

use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\UseCases\Quartos\ExcluirMidiaQuarto\ExcluirMidiaQuartoCommand;
use PHPUnit\Framework\TestCase;

/**
 * Class ExcluirMidiaQuartoCommandTest
 * @package Reservas\Tests\UseCases\Quartos\ExcluirMidiaQuarto
 * @coversDefaultClass \Reservas\UseCases\Quartos\ExcluirMidiaQuarto\ExcluirMidiaQuartoCommand
 */
class ExcluirMidiaQuartoCommandTest extends TestCase
{

    /**
     * @return ExcluirMidiaQuartoCommand
     */
    public function test__construct(): ExcluirMidiaQuartoCommand
    {
        $quarto = new Quarto('Teste', 1, 1);
        $arquivo = 'uploads/arquivos/teste.jpg';

        $command = new ExcluirMidiaQuartoCommand($quarto, $arquivo);

        $this->assertInstanceOf(ExcluirMidiaQuartoCommand::class, $command);

        return $command;
    }

    /**
     * @param ExcluirMidiaQuartoCommand $command
     * @covers ::getQuarto
     * @depends test__construct
     */
    public function test_GetQuarto(ExcluirMidiaQuartoCommand $command)
    {
        $this->assertInstanceOf(Quarto::class, $command->getQuarto());
    }

    /**
     * @param ExcluirMidiaQuartoCommand $command
     * @covers ::getArquivo
     * @depends test__construct
     */
    public function test_GetArquivo(ExcluirMidiaQuartoCommand $command)
    {
        $this->assertIsString($command->getArquivo());
    }
}
