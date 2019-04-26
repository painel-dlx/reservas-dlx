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

namespace Reservas\PainelDLX\Domain\Validators;


use Reservas\PainelDLX\Domain\Contracts\PedidoValidatorInterface;
use Reservas\PainelDLX\Domain\Entities\Pedido;
use Reservas\PainelDLX\Domain\Entities\Reserva;

/**
 * Class PedidoValidator
 * @package Reservas\PainelDLX\Domain\Validators
 */
class PedidoValidator extends AbstractValidator implements PedidoValidatorInterface
{
    /**
     * PedidoValidator constructor.
     * @param array $validators
     */
    public function __construct(array $validators)
    {
        $this->setValidators($validators);
    }

    /**
     * Valida uma determinada regra sobre reserva
     * @param Reserva $pedido
     * @return bool
     */
    public function validar(Pedido $pedido): bool
    {
        foreach ($this->getValidators() as $nomeValidator) {
            $validator = new $nomeValidator;

            if ($validator instanceof PedidoValidatorInterface) {
                $validator->validar($pedido);
            }
        }

        return true;
    }
}