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

namespace Reservas\Tests\Domain\Quartos\Entities;

use DateInterval;
use DatePeriod;
use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;
use Reservas\Domain\Quartos\Contracts\QuartoMidiaCollectionInterface;
use Reservas\Domain\Quartos\Entities\Quarto;

/**
 * Class QuartoTest
 * @package Reservas\Domain\Quartos\Entities
 * @coversDefaultClass \Reservas\Domain\Quartos\Entities\Quarto
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
        $this->assertEquals($qtde, $quarto->getQuantidade());
        $this->assertEquals($valor_min, $quarto->getValorMinimo());

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

    /**
     * @covers ::getDisponibilidade
     * @throws ReflectionException
     */
    public function test_GetDisponibilidade_deve_retornar_Collection_com_Disponibilidade_referente_ao_periodo()
    {
        $quarto = new Quarto('Teste', 1, 99);

        $data_inicial = new DateTime();
        $data_final = (clone $data_inicial)->modify('+1 day');

        $periodo = new DatePeriod($data_inicial, new DateInterval('P1D'), $data_final);

        foreach ($periodo as $data) {
            $quarto->addDisponibilidade($data, 1, [1 => 99], 0);
        }

        $rfx_disponibilidade = new ReflectionProperty($quarto, 'disponibilidade');
        $rfx_disponibilidade->setAccessible(true);

        $disponibilidade = $quarto->getDisponibilidade($data_inicial, $data_final);

        $this->assertCount(1, $rfx_disponibilidade->getValue($quarto));
        $this->assertCount(1, $disponibilidade);
    }
}
