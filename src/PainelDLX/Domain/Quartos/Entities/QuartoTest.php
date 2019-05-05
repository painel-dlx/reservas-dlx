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

namespace Reservas\PainelDLX\Domain\Quartos\Entities;

use PHPUnit\Framework\TestCase;
use Reservas\PainelDLX\Domain\Quartos\Contracts\QuartoMidiaCollectionInterface;

/**
 * Class QuartoTest
 * @package Reservas\PainelDLX\Domain\Quartos\Entities
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Quartos\Entities\Quarto
 */
class QuartoTest extends TestCase
{
    /**
     * @return Quarto
     */
    public function test__construct(): Quarto
    {
        $nome = 'Teste de Quarto';
        $qtde = 10;
        $valor_min = 10.00;

        $quarto = new Quarto($nome, $qtde, $valor_min);

        $this->assertInstanceOf(Quarto::class, $quarto);
        $this->assertEquals($nome, $quarto->getNome());
        $this->assertEquals($qtde, $quarto->getQtde());
        $this->assertEquals($valor_min, $quarto->getValorMin());

        // Collections
        // $this->assertInstanceOf(ArrayCollection::class, $quarto->getDispon());
        $this->assertInstanceOf(QuartoMidiaCollectionInterface::class, $quarto->getMidias());

        return $quarto;
    }

    /**
     * @param Quarto $quarto
     * @covers ::addMidia
     * @depends test__construct
     */
    public function test_AddMidia_deve_adicionar_uma_instancia_de_QuartoMidia(Quarto $quarto)
    {
        // Limpar as mídias de testes anteriores
        $quarto->getMidias()->clear();

        $arquivo = 'teste/teste/teste.png';
        $quarto->addMidia($arquivo);

        $has_arquivo = $quarto->getMidias()->hasArquivo($arquivo);

        $this->assertCount(1, $quarto->getMidias());
        $this->assertTrue($has_arquivo);
    }
}
