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
use Reservas\PainelDLX\Domain\Exceptions\NomeQuartoUtilizadoException;
use Reservas\PainelDLX\Domain\Repositories\QuartoRepositoryInterface;
use Reservas\PainelDLX\Domain\Validators\Quartos\VerificarNomeDuplicado;
use Reservas\PainelDLX\Tests\ReservasTestCase;

/**
 * Class VerificarNomeDuplicadoTest
 * @package Reservas\PainelDLX\Tests\PainelDLX\Validators\Quartos
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Validators\Quartos\VerificarNomeDuplicado
 */
class VerificarNomeDuplicadoTest extends ReservasTestCase
{
    /**
     * @throws NomeQuartoUtilizadoException
     * @covers ::executar
     */
    public function test_Executar_deve_lancar_uma_excecao_quando_o_nome_ja_estiver_sendo_utilizado()
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('existsOutroQuartoComMesmoNome')->willReturn(true);

        $this->expectException(NomeQuartoUtilizadoException::class);

        $quarto = new Quarto('Teste de quarto', 1, 10);
        (new VerificarNomeDuplicado($quarto_repository))->executar($quarto);
    }

    /**
     * @throws NomeQuartoUtilizadoException
     * @covers ::executar
     */
    public function test_Executar_deve_retornar_true_quando_o_nome_estiver_disponivel()
    {
        /** @var QuartoRepositoryInterface $quarto_repository */
        $quarto_repository = $this->createMock(QuartoRepositoryInterface::class);
        $quarto_repository->method('existsOutroQuartoComMesmoNome')->willReturn(false);

        $quarto = new Quarto('Teste de quarto', 1, 10);
        $is_disponivel = (new VerificarNomeDuplicado($quarto_repository))->executar($quarto);
        $this->assertTrue($is_disponivel);
    }
}
