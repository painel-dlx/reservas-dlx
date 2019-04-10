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

namespace Reservas\PainelDLX\Tests\Domain\Entities;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Reservas\PainelDLX\Domain\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Entities\Quarto;
use Reservas\PainelDLX\Tests\ReservasTestCase;

/**
 * Class DisponibilidadeTest
 * @package Reservas\PainelDLX\Domain\Entities
 * @coversDefaultClass \Reservas\PainelDLX\Domain\Entities\Disponibilidade
 */
class DisponibilidadeTest extends ReservasTestCase
{

    /**
     * @throws \Exception
     */
    public function test__construct(): Disponibilidade
    {
        $quarto = new Quarto('Teste', 10, 100);
        $hoje = new DateTime();
        $qtde_dispon = mt_rand(1, 10);

        $dispon = new Disponibilidade($quarto, $hoje, $qtde_dispon);

        $this->assertInstanceOf(Disponibilidade::class, $dispon);
        $this->assertEquals($quarto, $dispon->getQuarto());
        $this->assertEquals($hoje, $dispon->getDia());
        $this->assertEquals($qtde_dispon, $dispon->getQtde());
        $this->assertInstanceOf(Collection::class, $dispon->getValores());

        return $dispon;
    }

    /**
     * @param Disponibilidade $dispon
     * @covers ::addValor
     * @depends test__construct
     */
    public function test_AddValor_deve_criar_um_DisponValor(Disponibilidade $dispon)
    {
        $dispon->addValor(1, 12.34);
        $this->assertCount(1, $dispon->getValores());
    }

    /**
     * @param Disponibilidade $dispon
     * @covers ::getValorPorQtdePessoas
     * @depends test__construct
     */
    public function test_GetValorPorQtdePessoas_deve_retornar_valor_ref_qtde_pessoas(Disponibilidade $dispon)
    {
        $valor = 12.34;
        $qtde_pessoas = mt_rand(1, 10);

        $dispon->addValor($qtde_pessoas, $valor);
        $valor_dispon = $dispon->getValorPorQtdePessoas($qtde_pessoas);

        $this->assertIsFloat($valor_dispon);
        $this->assertEquals($valor, $valor_dispon);
    }

    /**
     * @param Disponibilidade $dispon
     * @covers ::getValorPorQtdePessoas
     * @depends test__construct
     */
    public function test_GetValorPorQtdePessoas_deve_retornar_null_quando_nao_encontrar_o_valor(Disponibilidade $dispon)
    {
        $valor_dispon = $dispon->getValorPorQtdePessoas(0);
        $this->assertNull($valor_dispon);
    }

    /**
     * @param Disponibilidade $dispon
     * @covers ::setValorPorQtdePessoas
     * @depends test__construct
     */
    public function test_SetValorPorQtdePessoas_adiciona_um_valor_ou_altera_o_valor_existente(Disponibilidade $dispon)
    {
        $valor_original = mt_rand(1, 10);
        $valor_novo = mt_rand(11, 20);

        $dispon->setValorPorQtdePessoas(1, $valor_original);

        $this->assertTrue($dispon->getValores()->count() > 0);
        $this->assertIsFloat($dispon->getValorPorQtdePessoas(1));
        $this->assertEquals($valor_original, $dispon->getValorPorQtdePessoas(1));

        $dispon->setValorPorQtdePessoas(1, $valor_novo);
        $this->assertIsFloat($dispon->getValorPorQtdePessoas(1));
        $this->assertEquals($valor_novo, $dispon->getValorPorQtdePessoas(1));
    }

    /**
     * @param Disponibilidade $dispon
     * @covers ::hasValorPorQtdePessoas
     * @depends test__construct
     */
    public function test_HasValorPorQtdePessoas_deve_retornar_true_quando_o_valor_estiver_configurado_ou_false_quando_nao(Disponibilidade $dispon)
    {
        $valor = mt_rand(11, 20);
        $dispon->setValorPorQtdePessoas(1, $valor);

        $this->assertTrue($dispon->hasValorPorQtdePessoas(1));
        $this->assertFalse($dispon->hasValorPorQtdePessoas(9999));
    }

    /**
     * @param Disponibilidade $dispon
     * @covers ::isPublicado
     * @depends test__construct
     */
    public function test_IsPublicado_deve_retornar_true_quando_todas_as_regras_forem_cumpridas(Disponibilidade $dispon)
    {
        $dispon->setQtde(1);

        for ($i = 1; $i < $dispon->getQuarto()->getQtde(); $i++) {
            $dispon->setValorPorQtdePessoas($i, 12.34);
        }

        $this->assertTrue($dispon->isPublicado());
    }

    /**
     * @param Disponibilidade $dispon
     * @covers ::isPublicado
     * @depends test__construct
     */
    public function test_IsPublicado_deve_retornar_false_quando_alguma_das_regras_nao_forem_cumpridas(Disponibilidade $dispon)
    {
        $dispon->setQtde(0);
        $this->assertFalse($dispon->isPublicado());
    }
}
