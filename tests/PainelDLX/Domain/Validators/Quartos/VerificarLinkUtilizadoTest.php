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

namespace Reservas\PainelDLX\Tests\PainelDLX\Validators\Quartos\Domain;

use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Domain\Exceptions\LinkQuartoUtilizadoException;
use Reservas\PainelDLX\Domain\Repositories\QuartoRepositoryInterface;
use Reservas\PainelDLX\Domain\Validators\Quartos\VerificarLinkUtilizado;
use PHPUnit\Framework\TestCase;

/**
 * Class VerificarLinkUtilizadoTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\Validators\Quartos
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Validators\Quartos\VerificarLinkUtilizado
 */
class VerificarLinkUtilizadoTest extends TestCase
{
    /**
     * @covers ::executar
     * @throws LinkQuartoUtilizadoException
     */
    public function test_Executar_deve_lancar_uma_excecao_quando_o_link_ja_estiver_sendo_utilizado()
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('existsOutroQuartoComMesmoLink')->willReturn(true);

        $this->expectException(LinkQuartoUtilizadoException::class);

        $quarto = new Quarto('Teste', 1, 10);
        $quarto->setLink('/teste/teste-url');
        (new VerificarLinkUtilizado($quarto_repository))->executar($quarto);
    }

    /**
     * @throws LinkQuartoUtilizadoException
     */
    public function test_Executar_deve_retornar_true_quando_o_link_estiver_disponivel()
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('existsOutroQuartoComMesmoLink')->willReturn(false);

        $quarto = new Quarto('Teste', 1, 10);
        $quarto->setLink('/teste/teste-url');

        $is_disponivel = (new VerificarLinkUtilizado($quarto_repository))->executar($quarto);
        $this->assertTrue($is_disponivel);
    }
}
