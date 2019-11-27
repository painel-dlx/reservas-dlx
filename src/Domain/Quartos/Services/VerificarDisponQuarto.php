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

namespace Reservas\Domain\Quartos\Services;

use DateTime;
use Reservas\Domain\Disponibilidade\Entities\Disponibilidade;
use Reservas\Domain\Quartos\Entities\Quarto;
use Reservas\Domain\Quartos\Exceptions\QuartoIndisponivelException;
use Reservas\Tests\Domain\Quartos\Services\VerificarDisponQuartoTest;

/**
 * Class VerificarDisponQuarto
 * @package Reservas\Domain\Quartos\Services
 * @covers VerificarDisponQuartoTest
 */
class VerificarDisponQuarto
{
    /**
     * @param Quarto $quarto
     * @return bool
     * @throws QuartoIndisponivelException
     */
    public function executar(Quarto $quarto, DateTime $checkin, DateTime $checkout): bool
    {
        $dispon_quarto = $quarto->getDisponibilidade($checkin, $checkout);

        if ($dispon_quarto->count() < 1) {
            throw QuartoIndisponivelException::nenhumaDisponibilidadeEncontrada($checkin, $checkout);
        }

        $dispon_quarto->map(function (Disponibilidade $dispon) {
            if (!$dispon->isPublicado()) {
                throw QuartoIndisponivelException::dataIndisponivel($dispon->getData());
            }
        });

        return true;
    }
}