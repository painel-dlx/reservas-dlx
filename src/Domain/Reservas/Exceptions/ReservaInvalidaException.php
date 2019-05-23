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

namespace Reservas\Domain\Reservas\Exceptions;


use DateTime;
use Exception;

class ReservaInvalidaException extends Exception
{
    /**
     * @param int $reserva_id
     * @return ReservaInvalidaException
     */
    public static function reservaJaCancelada(int $reserva_id)
    {
        return new self("A reserva #{$reserva_id} já está cancelada!", 10);
    }

    /**
     * @param int $reserva_id
     * @return ReservaInvalidaException
     */
    public static function reservaJaConfirmada(int $reserva_id)
    {
        return new self("A reserva #{$reserva_id} já está confirmada!", 11);
    }

    /**
     * @param DateTime $checkin
     * @return ReservaInvalidaException
     */
    public static function dataCheckinAnteriorHoje(DateTime $checkin)
    {
        return new self("Não é possível confirmar ou cancelar uma reserva após a data de checkin ({$checkin->format('d/m/Y')}).", 12);
    }

    /**
     * @param DateTime $checkin
     * @param DateTime $checkout
     * @return ReservaInvalidaException
     */
    public static function dataCheckoutMenorQueDataCheckin(DateTime $checkin, DateTime $checkout)
    {
        return new self("A data de checkin {$checkin->format('d/m/Y')} deve ser anterior a data de checkout {$checkout->format('d/m/Y')}.", 13);
    }
}