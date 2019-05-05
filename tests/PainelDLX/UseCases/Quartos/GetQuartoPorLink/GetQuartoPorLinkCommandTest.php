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

namespace Reservas\PainelDLX\Tests\UseCases\Quartos\GetQuartoPorLink;

use Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorLink\GetQuartoPorLinkCommand;
use PHPUnit\Framework\TestCase;

/**
 * Class GetQuartoPorLinkCommandTest
 * @package Reservas\PainelDLX\Tests\UseCases\Quartos\GetQuartoPorLink
 * @coversDefaultClass \Reservas\PainelDLX\UseCases\Quartos\GetQuartoPorLink\GetQuartoPorLinkCommand
 */
class GetQuartoPorLinkCommandTest extends TestCase
{
    /**
     * @return GetQuartoPorLinkCommand
     */
    public function test__construct(): GetQuartoPorLinkCommand
    {
        $command = new GetQuartoPorLinkCommand('nossos-quartos/quarto-teste');

        $this->assertInstanceOf(GetQuartoPorLinkCommand::class, $command);

        return $command;
    }

    /**
     * @param GetQuartoPorLinkCommand $command
     * @covers ::getLink
     * @depends test__construct
     */
    public function test_GetLink(GetQuartoPorLinkCommand $command)
    {
        $this->assertIsString($command->getLink());
    }
}
