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

namespace Reservas\UseCases\Quartos\VerificarDisponQuarto;


use Reservas\Domain\Quartos\Exceptions\QuartoIndisponivelException;
use Reservas\Domain\Quartos\Services\VerificarDisponQuarto;

class VerificarDisponQuartoCommandHandler
{
    /**
     * @param VerificarDisponQuartoCommand $command
     * @return bool Retorna true se o quarto está disponível para o período desejado
     * @throws QuartoIndisponivelException
     */
    public function handle(VerificarDisponQuartoCommand $command): bool
    {
        $quarto = $command->getQuarto();
        return (new VerificarDisponQuarto())->executar($quarto, $command->getCheckin(), $command->getCheckout());
    }
}