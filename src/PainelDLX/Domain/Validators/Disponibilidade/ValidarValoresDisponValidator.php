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

namespace Reservas\PainelDLX\Domain\Validators\Disponibilidade;


use Reservas\PainelDLX\Domain\Entities\Disponibilidade;
use Reservas\PainelDLX\Domain\Entities\DisponValor;
use Reservas\PainelDLX\Domain\Exceptions\ValorMenorQueMinimoQuartoException;

/**
 * Class ValidarValoresDisponValidator
 * @package Reservas\PainelDLX\Domain\Validators\Disponibilidade
 */
class ValidarValoresDisponValidator
{
    /**
     * @param Disponibilidade $dispon
     * @return bool
     */
    public function validar(Disponibilidade $dispon): bool
    {
        $valor_min = $dispon->getQuarto()->getValorMin();

        $dispon->getValores()->map(function (DisponValor $dispon_valor) use ($valor_min) {
            if ($valor_min > $dispon_valor->getValor()) {
                throw new ValorMenorQueMinimoQuartoException($valor_min);
            }
        });

        return true;
    }
}