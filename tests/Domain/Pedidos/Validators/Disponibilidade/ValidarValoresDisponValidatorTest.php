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

namespace Reservas\Tests\Domain\Validators\Disponibilidade;


use DateTime;
use Exception;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Disponibilidade\Exceptions\ValidarDisponibilidadeException;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Disponibilidade\Validators\ValidarValoresDisponValidator;
use Reservas\Tests\ReservasTestCase;

/**
 * Class ValidarValoresDisponValidatorTest
 * @package Reservas\Tests\Domain\Validators\Disponibilidade
 * @coversDefaultClass \Reservas\Domain\Disponibilidade\Validators\ValidarValoresDisponValidator
 */
class ValidarValoresDisponValidatorTest extends ReservasTestCase
{

    /**
     * @throws Exception
     * @covers ::validar
     */
    public function test_Validar_deve_lancar_excecao_quando_DisponValor_for_menor_que_minimo()
    {
        $quarto = new Quarto('Teste', 5, 100);
        $dispon = new Disponibilidade($quarto, new DateTime(), 1);
        $dispon->addValor(1, 12.34);

        $this->expectException(ValidarDisponibilidadeException::class);
        $this->expectExceptionCode(10);

        (new ValidarValoresDisponValidator())->validar($dispon);
    }

    /**
     * @throws Exception
     * @covers ::validar
     */
    public function test_Validar_deve_retornar_true_quando_DisponValor_for_valido()
    {
        $quarto = new Quarto('Teste', 5, 10);
        $dispon = new Disponibilidade($quarto, new DateTime(), 1);
        $dispon->addValor(1, 12.34);

        $is_valido = (new ValidarValoresDisponValidator())->validar($dispon);
        $this->assertTrue($is_valido);
    }
}
